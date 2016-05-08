<?php
namespace CartBundle\Model;
use CartBundle\Model\LogisticsBase;
use CartBundle\CartBundle;

class Logistics
    extends LogisticsBase
{

    public function dataLabel() {
        if ( $this->cost ) {
            // return $this->name . ' [$'. $this->cost . ']';
        }
        return $this->name;
    }

    public function getTrackingUrl($shippingId)
    {
        $bundle = CartBundle::getInstance();
        $trackerUrls = $bundle->config('TrackerUrls') ?: [ 
            // 中華郵政
            //    http://postserv.post.gov.tw/webpost/CSController?cmd=POS4009_2&MAILNO=RR060912937TW
            'post.gov.tw' => 'http://postserv.post.gov.tw/webpost/CSController?cmd=POS4009_2&MAILNO={id}',

            // 黑貓
            //    http://www.t-cat.com.tw/Inquire/Trace.aspx?no=3822885135
            //    http://www.t-cat.com.tw/Inquire/TraceDetail.aspx?BillID=3822885135&ReturnUrl=Trace.aspx
            't-cat.com.tw'    => 'http://www.t-cat.com.tw/Inquire/TraceDetail.aspx?BillID={id}&ReturnUrl=Trace.aspx',


            // 新竹貨運
            // 'http://cagweb01.hct.com.tw/pls/cagweb/C_PIKAM020AS?pACT=C_POKAM31&pINVOICE_NO=1016975153&pNo=1016975153',
            'hct.com.tw' => 'http://cagweb01.hct.com.tw/pls/cagweb/C_PIKAM020AS?pACT=C_POKAM31&pINVOICE_NO={id}&pNo={id}',

            // 宅配通
            'e-can.com.tw' => 'http://query2.e-can.com.tw/self_link/id_link.asp?txtMainID={id}',
        ];
        if ($this->handle) {
            if ( isset($trackerUrls[ $this->handle ]) ) {
                return str_replace('{id}', $shippingId,$trackerUrls[ $this->handle ]);
            } else {

            }
        }
        return;
    }
}
