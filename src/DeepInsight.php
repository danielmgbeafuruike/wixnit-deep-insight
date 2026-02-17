<?php

    namespace Wixnit\DeepInsight;

    use Exception;
    use PHPMailer\PHPMailer\PHPMailer;
    use Wixnit\App\api;
    use Wixnit\DeepInsight\Auth\DeepInsightAuth;
    use Wixnit\DeepInsight\Auth\DeepInsightAuthBuffer;
    use Wixnit\DeepInsight\Template\HTMLTemplate;
    use Wixnit\Enum\HTTPResponseCode;
    use Wixnit\Routing\Response;

    class DeepInsight
    {
        public static function Init(): DeepInsight
        {
            $ret = new DeepInsight();
            return $ret;
        }


        /**
         * Begin Authentication of the user
         * @return void
         */
        public function initAuthentication(string $toEmail, string $deviceId): void
        {
            $otp = DeepInsightAuthBuffer::Create($deviceId);
            $otp->save();

            $mail = new PHPMailer();

            try {
                //Recipients
                $mail->setFrom('no-reply@deep-insight.alphacheq.com', 'Wixnit Deep Insight');
                $mail->addAddress($toEmail);     //Add a recipient
                $mail->addReplyTo('support@deep-insight.alphacheq.com', 'Wixnit Deep Insight');

                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = 'Use the OTP to connect to your Wixnit Deep Insight Client';
                $mail->Body    = HTMLTemplate::verification($otp->otp);
                $mail->AltBody = HTMLTemplate::verificationAlt($otp->otp);


                $mail->send();
                
                
                (new Response())
                    ->json(api::Success($otp->id, "client connection initiated"))
                    ->send();
            }
            catch (Exception $e) 
            {
                (new Response(HTTPResponseCode::BAD_REQUEST))
                    ->json(api::BadRequest("Message could not be sent. Mailer Error: {$mail->ErrorInfo}"))
                    ->send();
            }
        }


        /**
         * Verify and connect user
         * @param string $bufferId
         * @param string $deviceId
         * @param string $otp
         * @param string $fcmToken
         * @return void
         */
        public function verifyConnectionOTP(string $bufferId, string $deviceId, string $otp, string $fcmToken, string $clientName): void
        {
            $buffer = new DeepInsightAuthBuffer($bufferId);
            
            if($buffer->id != "")
            {
                if(($buffer->deviceId == $deviceId) && ($buffer->otp == $otp))
                {
                    $auth = DeepInsightAuth::Create($fcmToken, $deviceId);
                    $auth->clientName = $clientName;
                    $auth->save();

                    $buffer->delete();

                    (new Response())
                        ->json(api::Success($auth->token, "client connected"))
                        ->send();
                }
                else
                {
                    (new Response(HTTPResponseCode::UNAUTHORIZED))
                        ->json(api::Unauthorized("invalid OTP. could not authenticate the client"))
                        ->send();
                }
            }
            else
            {
                (new Response(HTTPResponseCode::UNAUTHORIZED))
                    ->json(api::Unauthorized("could not authenticate the client"))
                    ->send();
            }
        }
    }