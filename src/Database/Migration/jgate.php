<?php

namespace Modules\jgate\Database\Migration;

use Joonika\Database;
use Joonika\Migration\Migration;

class jgate extends Migration
{
    public function init()
    {
        return [
            "jgate.jgate_cache" => [
                "version" => 1,
                "columns" => [
                    "id" => $this->id_structure(true),
                    "datetime" => $this->datetime(true),
                    "gatewaySlug" => $this->varchar(50),
                    "serviceSlug" => $this->varchar(50),
                    "expireDate" => $this->datetime(false),
                    "input" => $this->varchar(),
                    "output" => $this->text(),
                    "status" => $this->int(1),
                ],
                'indexes' => [
                    "serviceSlug,gatewaySlug,expireDate,input",
                ]
            ],
            "jgate.jgate_gateways" => [
                "version" => 1,
                "columns" => [
                    "id" => $this->id_structure(),
                    "createdBy" => $this->int(),
                    "createdOn" => $this->datetime(true),
                    "title" => $this->varchar(),
                    "mainAddress" => $this->varchar(),
                    "apiKey" => $this->varchar(),
                    "lastToken" => $this->varchar(),
                    "lastTokenDate" => $this->datetime(false),
                    "status" => $this->varchar(20, true, "'active'"),
                    "coreType" => $this->varchar(255, true, "'Joonika'"),
                    "sort" => $this->int(0),
                    "lastCheckToken" => $this->text(),
                    "slugControl" => $this->varchar(),
                    "type" => $this->varchar(10, true, "'main'"),
                    "servicesJson" => $this->text(),
                ],
                "indexes" => [
                    "status",
                    "lastTokenDate",
                ],
            ],
            "jgate.jgate_services" => [
                "version" => 1,
                "columns" => [
                    "id" => $this->id_structure(true),
                    "slug" => $this->varchar(),
                    "title" => $this->varchar(),
                    "type" => $this->varchar(20, true, "'json'"),
                    "status" => $this->int(1),
                ],
                "indexes" => [
                    "slug",
                    "status",
                    "slug,status",
                ],
            ],
            "jgate.jgate_services_rel" => [
                "version" => 1,
                "columns" => [
                    "id" => $this->id_structure(),
                    "gId" => $this->int(),
                    "sId" => $this->int(),
                    "address" => $this->varchar(),
                    "status" => $this->int(1),
                    "sort" => $this->int(0),
                ],
                "indexes" => [
                    "gId",
                    "sId",
                    "gId,sId",
                    "gId,sId,status",
                ],
            ],
        ];
    }
}