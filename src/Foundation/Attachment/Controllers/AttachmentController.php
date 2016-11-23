<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2016, iBenchu.org
 * @datetime 2016-11-02 15:55
 */
namespace Notadd\Foundation\Attachment\Controllers;

use Notadd\Foundation\Attachment\Handlers\AttachmentSetHandler;
use Notadd\Foundation\Routing\Abstracts\Controller;
use Notadd\Foundation\Setting\Contracts\SettingsRepository;

/**
 * Class AttachmentApi.
 */
class AttachmentController extends Controller
{
    /**
     * @param \Notadd\Foundation\Setting\Contracts\SettingsRepository $settings
     *
     * @return \Psr\Http\Message\ResponseInterface|\Zend\Diactoros\Response
     */
    public function handle(SettingsRepository $settings)
    {
        $handler = new AttachmentSetHandler($this->container, $settings);
        $response = $handler->toResponse($this->request);

        return $response->generateHttpResponse();
    }
}