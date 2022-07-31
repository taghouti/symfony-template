<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Config
 *array:6 [▼
0 => App\Entity\Config {#860 ▼
-id: 1
-configName: "API KEY"
-configValue: "b5d8d7c4-1f93-4584-9ef3-7855af11a960"
}
1 => App\Entity\Config {#862 ▼
-id: 2
-configName: "SMTP USER"
-configValue: "R-MAX@imh-service.com"
}
2 => App\Entity\Config {#863 ▼
-id: 3
-configName: "SMTP PASS"
-configValue: "Test1234!"
}
3 => App\Entity\Config {#864 ▼
-id: 4
-configName: "SMTP HOST"
-configValue: "ssl0.ovh.net"
}
4 => App\Entity\Config {#865 ▼
-id: 5
-configName: "SMTP PORT"
-configValue: "465"
}
5 => App\Entity\Config {#866 ▼
-id: 6
-configName: "SMTP PROTOCOL"
-configValue: "SSL/TLS"
}
]
 * @ORM\Table(name="config")
 * @ORM\Entity
 */
class Config
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="config_name", type="text", length=65535, nullable=true)
     */
    private $configName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConfigName(): ?string
    {
        return $this->configName;
    }

    public function setConfigName(?string $configName): self
    {
        $this->configName = $configName;

        return $this;
    }

    /**
     * @var string|null
     *
     * @ORM\Column(name="config_value", type="text", length=65535, nullable=true)
     */
    private $configValue;

    function getConfigValue(): ?string
    {
        return $this->configValue;
    }

    public function setConfigValue(?string $configValue): self
    {
        $this->configValue = $configValue;

        return $this;
    }

}
