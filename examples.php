<?php

/**
 * This file contains aexamples for the Bits on the Run API client
 *
 * PHP version 5, 7
 *
 * LICENSE:
 *
 * Copyright 2012 LongTail Ad Solutions
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * 3. Neither the name of the copyright holder nor the names of its contributors
 *    may be used to endorse or promote products derived from this software
 *    without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Services
 * @package   BotrAPI
 * @author    Sergey Lashin <support@jwplayer.com>
 * @copyright 2012 LongTail Ad Solutions
 * @license   BSD 3-Clause License
 * @link      https://github.com/silverorange/botr-api
 */

header('Content-type: text/plain; charset=utf-8');

// Please update xxxx with your key and yyyy with your secret
$botr_api = new BotrAPI('xxxxxxxx', 'yyyyyyyyyyyyyyyyyyyyyyyy');

// Here's an example call that lists all videos.
print_r($botr_api->call("/videos/list"));

// Video details example; update zzzz with a video_key listed by the call above.
// print_r($botr_api->call("/videos/show", array('video_key' => 'zzzzzzzz')));

// Thumbnail upload example; again replace zzzz with your video key.
/*
$response = $botr_api->call("/videos/thumbnails/update", array('video_key' => 'zzzzzzzz'));
if ($response['status'] == "error") {
    print_r($response);
} else {
    $response = $botr_api->upload($response['link'], "./thumbnail.jpg");
    print_r($response);
}
*/

?>
