<?php

    namespace Wixnit\DeepInsight;

    use Wixnit\Routing\Request;
    use Wixnit\Utilities\Date;
    use Wixnit\Utilities\Timespan;

    class InsightRequestTimeSpan
    {
        public Timespan $value;

        public function __construct(Request | null $req=null)
        {
            $this->value = new Timespan(time(), time(), true);

            if($req != null)
            {
                if(isset($req['from_date']) && isset($req['to_date']))
                {
                    $start_date = new Date($req['from_date']);
                    $end_date = new Date($req['to_date']);

                    if($start_date->toEpochSeconds() == $end_date->toEpochSeconds())
                    {
                        $start = new Date(strtotime($start_date->month."/".$start_date->day."/".$start_date->year));
                        $end = new Date(strtotime($start_date->month."/".$start_date->day."/".$start_date->year) + ((60 * 60) * 24));

                        $this->value = new Timespan($start, $end);
                    }
                    else
                    {
                        $this->value = new Timespan($start_date, $end_date);
                    }
                }
            }
        }

        public static function fromDates(Date $fromDate, Date $toDate)
        {
            if($fromDate->toEpochSeconds() == $toDate->toEpochSeconds())
            {
                $toDate->hour += 1;
            }

            $ret = new InsightRequestTimeSpan();
            $ret->value->start = $fromDate;
            $ret->value->stop = $toDate;

            return $ret;
        }
    }