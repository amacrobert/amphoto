<?php

namespace AppBundle\Service;

use AppBundle\Entity\Freebie;
use Mailgun\Connection\Exceptions\MissingRequiredParameters;

class FreebieService {

    private $em;
    private $mailgun;

    public function __construct($em, $mailgun) {
        $this->em = $em;
        $this->mailgun = $mailgun;
    }

    public function mail($email, Freebie $freebie) {
        $result = $this->mailgun->messages()->send('andrewmacrobert.com', [
            'from'          => 'downloads@andrewmacrobert.com',
            'to'            => $email,
            'subject'       => $freebie->getName(),
            'html'          => $freebie->getEmailBody(),
            'attachment'    => [
                [
                    'filePath' => $freebie->getUri(),
                    'filename' => $freebie->getFilename(),
                ],
            ],
        ]);
    }

    public function subscribe($email) {
        try {
            $this->mailgun->post('lists/photographers@andrewmacrobert.com/members', [
                'address'       => $email,
                'subscribed'    => true,
            ]);
        }
        catch (MissingRequiredParameters $e) {
            // user already subscribed
        }
    }
}
