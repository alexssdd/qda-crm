<?php

namespace app\modules\mail;

use Exception;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Mail API
 */
class MailApi
{
    use ModuleTrait;

    private $mailer;

    /**
     * @param $to
     * @param $subject
     * @param $text
     * @return void
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function sendMessage($to, $subject, $text): void
    {
        $mailer = $this->getMailer();

        // Создаём письмо
        $email = (new Email())
            ->from($this->getUser())
            ->to($to)
            ->subject($subject)
            ->text($text);

        $mailer->send($email);
    }

    /**
     * @return Mailer
     * @throws Exception
     */
    protected function getMailer(): Mailer
    {
        if (!is_object($this->mailer)) {
            // Variables
            $smtpHost = $this->getModule()->getSmtpHost();
            $smtpPort = $this->getModule()->getSmtpPort();
            $smtpUser = $this->getUser();
            $smtpPassword = $this->getModule()->getSmtpPassword();

            // Prepare dsn
            $dsn = sprintf('smtp://%s:%s@%s:%d', urlencode($smtpUser), urlencode($smtpPassword), $smtpHost, $smtpPort);

            // Prepare transport
            $transport = Transport::fromDsn($dsn);

            // Prepare mailer
            $this->mailer = new Mailer($transport);
        }

        return $this->mailer;
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function getUser(): string
    {
        return $this->getModule()->getSmtpUser();
    }
}