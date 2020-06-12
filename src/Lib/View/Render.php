<?php

namespace Digikala\Lib\View;

use Digikala\Repository\NonPersistence\NotificationInMemoryRepository;
use Digikala\Services\MemcachedService;
use Digikala\Storage\MemcachedCacheStorage;

/**
 * Class Render
 * @package Digikala\Lib\View
 */
class Render
{
    /**
     * @return string
     * @throws \Assert\AssertionFailedException
     */
    public function html()
    {
        $memcached = new MemcachedService();

        $html = '<html >
        <style type="text/css">
            .tg  {border-collapse:collapse;border-spacing:0;}
            .tg td{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
              overflow:hidden;padding:10px 5px;word-break:normal;}
            .tg th{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
              font-weight:normal;overflow:hidden;padding:10px 5px;word-break:normal;}
            .tg .tg-0lax{text-align:left;vertical-align:top}
            </style><style type="text/css">
            .tg  {border-collapse:collapse;border-spacing:0;}
            .tg td{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
              overflow:hidden;padding:10px 5px;word-break:normal;}
            .tg th{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
              font-weight:normal;overflow:hidden;padding:10px 5px;word-break:normal;}
            .tg .tg-0lax{text-align:left;vertical-align:top}
        </style><body>';
        $all = !empty($memcached->get(NotificationInMemoryRepository::ALL_MSG)) ? $memcached->get(NotificationInMemoryRepository::ALL_MSG) : 0 ;
        $html .= '<div> all msg: ' . $all  . '</div>';

        $html .= '<div >top 10 : <br/>';
        foreach ($memcached->get(NotificationInMemoryRepository::TOP_TEN) as $value) {
            $html .= '<li><div> ' . $value['mobile'] . ' </div></li>';
        }

        $fault =  empty($memcached->get(NotificationInMemoryRepository::API_FAULT)) ? 0 : $memcached->get(NotificationInMemoryRepository::API_FAULT)[0]['c'] ;
        $html .= '<br/><div >all fault: ' . $fault . '</div>';
        $usage = empty($memcached->get(NotificationInMemoryRepository::API_USAGE))? 0 : $memcached->get(NotificationInMemoryRepository::API_USAGE)[0]['c'];
        $html .= '<br/><div >usage : ' . $usage . '</div>';
        $html .= '<br/><form ><input name="search" type="text"><input type="submit" value="search by number"> </form>';

        if ($_GET['search']) {
            $result = (new NotificationInMemoryRepository(new MemcachedCacheStorage($memcached)))->findByIndex(NotificationInMemoryRepository::MOBILE_INDEX, $_GET['search']);
            if (!empty($result)) {
                $html .= '<table class="tg">
                            <thead>
                              <tr>
                                <th class="tg-0lax">id</th>
                                <th class="tg-0lax">mobile</th>
                                <th class="tg-0lax">body</th>
                                <th class="tg-0lax">status</th>
                                <th class="tg-0lax">server</th>
                                <th class="tg-0lax">port</th>
                                <th class="tg-0lax">type</th>
                                <th class="tg-0lax">created</th>
                              </tr>
                            </thead>';
                foreach ($result as $item) {
                    $html .= '<tbody>
                        <td class="tg-0lax">' . $item['id'] . '</td>
                        <td class="tg-0lax">' . $item['mobile'] . '</td>
                        <td class="tg-0lax">' . $item['body'] . '</td>
                        <td class="tg-0lax">' . $item['status'] . '</td>
                        <td class="tg-0lax">' . $item['server'] . '</td>
                        <td class="tg-0lax">' . $item['port'] . '</td>
                        <td class="tg-0lax">' . $item['type'] . '</td>
                        <td class="tg-0lax">' . $item['created'] . '</td>
                        </tbody>';
                }
                $html .= '</table>';
            } else {

                $html .= '<div> not found </div>';
            }
        }
        $html .= '</body></html>';

        return $html;
    }
}
