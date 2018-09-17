<?php

namespace App\Controller\Api;

use App\Entity\Customer;
use App\Entity\Meter;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use App\Entity\MeterReads;
use App\Repository\MeterReadsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class APIController extends Controller {

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * List all meter-reads for a specific customerId and mpxn
     *
     * @FOSRest\Get("/meter-read")
     *
     * @return array
     */
    public function getMeterReadsAction(Request $request)
    {
        // get and validate input parameters
        $customerId = $request->query->get('customerId');
        $mpxn       = $request->query->get('mpxn');

        if (!$customerId || !$mpxn){
            return $this->json("Cant Execute GET request. customerID or mpxn parameters missing", 422) ;
        }

        // get and validate customer and meter
        /** @var Customer $customer*/
        $customer = $this->getDoctrine()->getRepository(Customer::class)->findOneByCustomerId($customerId);
        $meter    = $this->getDoctrine()->getRepository(Meter::class)->findOneByMpxnAndCustomerId($mpxn,$customerId);

        if (!$customer){

            return $this->json('Customer '.$customerId.' not registered in DB') ;
        }
        else if (!$meter){

            return $this->json('MPRN or MPAN: '.$mpxn.' not registered in DB or not associated anymore to the customerId provided') ;
        }
        else{

            // get all reads for this Customer and Meter(MPXN)
            /** @var MeterReads[] $meterRead */
            $meterReads = $this->getDoctrine()->getRepository(MeterReads::class)->findAllByMeterId($meter->getId());

            $refactoredMeterReads = [];
            foreach ($meterReads as $read) {
                // Date Format Conversion
                $datetime = (new \DateTime())->setTimestamp($read->getReadDate()->getTimestamp())->setTimezone(new \DateTimeZone('UTC'));

                $refactoredMeterReads[] = [
                    'type'          => $read->getType(),
                    'registerId'    => $read->getRegisterId(),
                    'value'         => $read->getValue(),
                    'readType'      => $read->getReadType(),
                    'readDate'      => $datetime->format(\DateTime::ISO8601),
                ];
            }

            return $this->json([
                "customerId"   => $customerId,
                "serialNumber" => $meter->getSerialNumber(),
                "mpxn"         => $mpxn,
                "read"         => $refactoredMeterReads
                ],200);
        }
    }

    /**
     * Create a new Meter Read
     *
     * @FOSRest\Post("/meter-read")
     *
     * @return array
     */
    public function postMeterReadAction(Request $request)
    {
        // validate POST input parameters
        $validatedParameters = $this->validatePostParameters($request);
        if ($validatedParameters['errorStatus'] != null){
            return $this->json($validatedParameters['errorStatus'], 200);
        }

        // check if is a new Customer. If doesn't exist make a new Customer
        $repository = $this->getDoctrine()->getRepository(Customer::class);

        /** @var Customer[] $customer */
        $customer = $repository->findOneByCustomerId($validatedParameters['customerId']);

        if (!$customer){

            $customer = new Customer();
            $customer->setId($validatedParameters['customerId']);

            $this->em->persist($customer);
            $this->em->flush();
        }

        // check if it is a new Meter. If doesn't exist make a new Meter
        $repository = $this->getDoctrine()->getRepository(Meter::class);

        /** @var Meter[] $meter */
        $meter = $repository->findOneByMpxnAndCustomerId($validatedParameters['mpxn'],$validatedParameters['customerId']);

        if (!$meter){

            $meter = new Meter();
            $meter->setMpxn($validatedParameters['mpxn']);
            $meter->setSerialNumber($validatedParameters['serialNumber']);
            $meter->setCustomer($validatedParameters['customerId']);

            $this->em->persist($meter);
            $this->em->flush();
        }


        // register a meter read for the current MPXN (if there are more "type" of read, you need to POST it separately)
        $meterRead = new MeterReads();

        $meterRead->setMeter($meter->getId());
        $meterRead->setType($validatedParameters['type']);
        $meterRead->setRegisterId($validatedParameters['registerId']);
        $meterRead->setValue($validatedParameters['value']);
        $meterRead->setReadType($validatedParameters['readType']);
        $meterRead->setReadDate(new \DateTime());

        // save it in DB
        $this->em->persist($meterRead);
        $this->em->flush();

        return $this->json($meterRead, 200);
    }

    /**
     * Validate Post Parameters
     *
     * @return array
     */
    public function validatePostParameters(Request $request)
    {
        // get POST parameters
        $inputParameters = [
            'customerId'    => $request->get('customerId'),
            'mpxn'          => $request->get('mpxn'),
            'serialNumber'  => $request->get('serialNumber'),
            'type'          => $request->get('type'),
            'registerId'    => $request->get('registerId'),
            'value'         => $request->get('value'),
        ];

        // retreive invalid parameters and retreive an error if something is not set
        $invalidParameters = array();
        foreach(array_keys($inputParameters) as $key) {

           (!$inputParameters[$key]) ? array_push($invalidParameters,$key) : null;
        }

        if (!empty($invalidParameters)){
            return [
                "errorStatus" => "[".implode($invalidParameters," , ")."] parameters missing. Can't execute POST action."
            ];
        }

        // retreive mpxn type (ELECT = 21 digits // GAS = 6-10 digits) and return error if have invalid format
        $mpxnLenght = strlen($request->get('mpxn'));

        if ($mpxnLenght == 21){
            $inputParameters['readType'] = "ELECTRICITY";
        }
        else if ($mpxnLenght >= 6 && $mpxnLenght <= 11 ){
            $inputParameters['readType'] = "GAS";
        }
        else
            $inputParameters['readType'] = "ND";

        // return error if mpxn is not a MPAN or a MPRN
        if ($inputParameters['readType'] == "ND") {
            return [
                "errorStatus" => "MPXN format not valid. Only 6-11 (GAS/MPRN) or 21(ELECTRICITY/MPAN) digits values are allowed. Your parameter have ".$mpxnLenght." digits"
            ];
        }

        // if POST parameters are validated. Return an array with POST values in a more readable format
        $inputParameters['errorStatus'] = null;

        return $inputParameters;
    }
}