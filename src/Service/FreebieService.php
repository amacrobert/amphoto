<?php

namespace App\Service;

use App\Entity\Freebie;
use App\Entity\User;
use Mailgun\Connection\Exceptions\MissingRequiredParameters;

class FreebieService {

    private $em;
    private $mailgun;

    public function __construct($em, $mailgun) {
        $this->em = $em;
        $this->mailgun = $mailgun;
    }

    public function mail($email, Freebie $freebie) {
        $email = strtolower($email);

        $result = $this->mailgun->messages()->send('mg.andrewmacrobert.com', [
            'from'          => '"Andrew MacRobert" <downloads@andrewmacrobert.com>',
            'to'            => $email,
            'subject'       => $freebie->getName(),
            'html'          => $freebie->getEmailBody(),
            'attachment'    => [
                [
                    'filePath' => __DIR__ . '/../../../web' . $freebie->getUri(),
                    'filename' => $freebie->getFilename(),
                ],
            ],
        ]);

        // Register the user's email address and mark that they downloaded this freebie
        if (!$user = $this->em->getRepository(User::class)->findOneBy(['email' => $email])) {
            $user = new User;
            $user->setEmail($email);
            $this->em->persist($user);
        }
        if (!$user->getFreebies()->contains($freebie)) {
            $user->addFreebie($freebie);
        }
        $this->em->flush();
    }

    public function subscribe($email) {
        $email = strtolower($email);

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
