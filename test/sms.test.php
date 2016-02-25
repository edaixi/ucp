<?php
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';

$request = array (
		'phone' => '18910361130',
		'content' => '12345678sdfdsf你好555555' 
);
$job_name = 'SMS_JOB';

Resque::setBackend ( '127.0.0.1:6379' );
//weixin icoupon_template
//*
$channel = 'high';
$jobId = Resque::enqueue ( $channel, $job_name, $request, true );
echo "Queued job " . $jobId . "\n\n";

