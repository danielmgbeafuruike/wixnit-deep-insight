<?php

    namespace Wixnit\DeepInsight\Auth;

    use Wixnit\App\Model;
    use Wixnit\Data\Filter;
    use Wixnit\Enum\CharacterType;
    use Wixnit\Utilities\Random;

    class DeepInsightAuthBuffer extends Model
    {
        public string $otp;
        public string $deviceId;


        public static function Create(string $deviceId): DeepInsightAuthBuffer
        {
            $buffer = new DeepInsightAuthBuffer();

            $existng = DeepInsightAuthBuffer::Get(new Filter([
                'deviceid'=> $deviceId,
            ]));

            if($existng->count() > 0)
            {
                $buffer = $existng[0];    
            }
            $buffer->deviceId = $deviceId;

            return $buffer;
        }


        protected function onPreSave(): void
        {
            if($this->otp == "")
            {
                $this->otp = Random::Characters(6, CharacterType::NUMERIC);
            }
        }
    }