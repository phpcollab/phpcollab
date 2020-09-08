<?php


namespace phpCollab\Members;


use Exception;
use Monolog\Logger;
use Sabre\VObject\Component\VCard;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

class ExportMember
{
    /**
     * @param array $userDetail
     * @param VCard $vcard
     * @param Logger $logger
     */
    public static function generateVcard(array $userDetail, VCard $vcard, Logger $logger)
    {
        try {
            if (!empty($userDetail["mem_name"])) {
                $vcard->add('FN', $userDetail["mem_name"]);

                if (!empty($userDetail["mem_title"])) {
                    $vcard->add('TITLE', $userDetail["mem_title"]);
                }
                if (!empty($userDetail["org_name"])) {
                    $vcard->add('ORG', $userDetail["org_name"]);
                }
                if (!empty($userDetail["mem_email_work"])) {
                    $vcard->add('EMAIL', $userDetail["mem_email_work"]);
                }
                if (!empty($userDetail["mem_phone_work"])) {
                    $vcard->add('TEL', $userDetail["mem_phone_work"], ['type' => 'work']);
                }
                if (!empty($userDetail["mem_phone_home"])) {
                    $vcard->add('TEL', $userDetail["mem_phone_home"], ['type' => 'home']);
                }
                if (!empty($userDetail["mem_mobile"])) {
                    $vcard->add('TEL', $userDetail["mem_mobile"], ['type' => 'cell']);
                }
                if (!empty($userDetail["mem_fax"])) {
                    $vcard->add('TEL', $userDetail["mem_fax"], ['type' => 'fax']);
                }

                $filename = $userDetail["mem_name"] . '.vcf';

                self::sendVcard($vcard, $filename);
            }
        } catch (Exception $exception) {
            $logger->error('vCard error: ' . $exception->getMessage());
        }
    }

    /**
     * @param VCard $cardContent
     * @param string $filename
     */
    private static function sendVcard(VCard $cardContent, string $filename)
    {
        $response = new Response($cardContent->serialize());

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->send();
    }
}
