<?php


namespace Modules\jgate\inc;


class Configs
{

    public static function menus()
    {
        return [
            "title"=>__("jgate"),
            "link" => "#",
            "name" => "jgate",
            "icon" => "fal fa-map-marker-alt",
            "sub" => [
                [
                    "title" => __("services list"),
                    "link" => "services/index",
                    "name" => "services",
                    "icon" => "fal fa-list",
                ],
                [
                    "title" => __("gateways list"),
                    "link" => "gateways/index",
                    "name" => "gateways",
                    "icon" => "fal fa-cogs",
                ]
            ],
        ];
    }
    public static function gatewayConfigTabs()
    {
        return [
            [
                "link" => 'stats',
                "permissions" => "gateway_manage",
                "icon" => "fal fa-chart-bar",
                "title" => __("stats")
            ],[
                "link" => 'agents',
                "permissions" => "gateway_manage",
                "icon" => "fal fa-key",
                "title" => __("agents")
            ],[
                "link" => 'services',
                "permissions" => "gateway_manage",
                "icon" => "fal fa-key",
                "title" => __("services")
            ],[
                "link" => 'logs',
                "permissions" => "gateway_manage",
                "icon" => "fal fa-key",
                "title" => __("logs")
            ]
        ];
    }

}