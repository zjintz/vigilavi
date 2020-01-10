<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogEntryRepository")
 */
class LogEntry
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $device;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="time")
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $timezone;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $device_name;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $device_id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $log_id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $log_type;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $log_component;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $log_subtype;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $priority;

    /**
     * @ORM\Column(type="integer")
     */
    private $fw_rule_id;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $user_name;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $user_gp;

    /**
     * @ORM\Column(type="integer")
     */
    private $iap;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $category;

    /**
     * @ORM\Column(type="text")
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $contenttype;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $override_token;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $httpresponsecode;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $src_ip;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $dst_ip;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $protocol;

    /**
     * @ORM\Column(type="integer")
     */
    private $src_port;

    /**
     * @ORM\Column(type="integer")
     */
    private $dst_port;

    /**
     * @ORM\Column(type="integer")
     */
    private $sent_bytes;

    /**
     * @ORM\Column(type="integer")
     */
    private $recv_bytes;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $domain;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $exceptions;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $activityname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reason;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $user_agent;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $status_code;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $transactionid;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $referer;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $download_file_name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $download_file_type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $upload_file_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $upload_file_type;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $con_id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $application;

    /**
     * @ORM\Column(type="boolean")
     */
    private $app_is_cloud;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $override_name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $override_authorizer;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $category_type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(string $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getDeviceName(): ?string
    {
        return $this->device_name;
    }

    public function setDeviceName(string $device_name): self
    {
        $this->device_name = $device_name;

        return $this;
    }

    public function getDeviceId(): ?string
    {
        return $this->device_id;
    }

    public function setDeviceId(string $device_id): self
    {
        $this->device_id = $device_id;

        return $this;
    }

    public function getLogId(): ?string
    {
        return $this->log_id;
    }

    public function setLogId(string $log_id): self
    {
        $this->log_id = $log_id;

        return $this;
    }

    public function getLogType(): ?string
    {
        return $this->log_type;
    }

    public function setLogType(string $log_type): self
    {
        $this->log_type = $log_type;

        return $this;
    }

    public function getLogComponent(): ?string
    {
        return $this->log_component;
    }

    public function setLogComponent(string $log_component): self
    {
        $this->log_component = $log_component;

        return $this;
    }

    public function getLogSubtype(): ?string
    {
        return $this->log_subtype;
    }

    public function setLogSubtype(string $log_subtype): self
    {
        $this->log_subtype = $log_subtype;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getFwRuleId(): ?int
    {
        return $this->fw_rule_id;
    }

    public function setFwRuleId(int $fw_rule_id): self
    {
        $this->fw_rule_id = $fw_rule_id;

        return $this;
    }

    public function getUserName(): ?string
    {
        return $this->user_name;
    }

    public function setUserName(?string $user_name): self
    {
        $this->user_name = $user_name;

        return $this;
    }

    public function getUserGp(): ?string
    {
        return $this->user_gp;
    }

    public function setUserGp(?string $user_gp): self
    {
        $this->user_gp = $user_gp;

        return $this;
    }

    public function getIap(): ?int
    {
        return $this->iap;
    }

    public function setIap(int $iap): self
    {
        $this->iap = $iap;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getContenttype(): ?string
    {
        return $this->contenttype;
    }

    public function setContenttype(?string $contenttype): self
    {
        $this->contenttype = $contenttype;

        return $this;
    }

    public function getOverrideToken(): ?string
    {
        return $this->override_token;
    }

    public function setOverrideToken(?string $override_token): self
    {
        $this->override_token = $override_token;

        return $this;
    }

    public function getHttpresponsecode(): ?string
    {
        return $this->httpresponsecode;
    }

    public function setHttpresponsecode(?string $httpresponsecode): self
    {
        $this->httpresponsecode = $httpresponsecode;

        return $this;
    }

    public function getSrcIp(): ?string
    {
        return $this->src_ip;
    }

    public function setSrcIp(string $src_ip): self
    {
        $this->src_ip = $src_ip;

        return $this;
    }

    public function getDstIp(): ?string
    {
        return $this->dst_ip;
    }

    public function setDstIp(string $dst_ip): self
    {
        $this->dst_ip = $dst_ip;

        return $this;
    }

    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    public function setProtocol(string $protocol): self
    {
        $this->protocol = $protocol;

        return $this;
    }

    public function getSrcPort(): ?int
    {
        return $this->src_port;
    }

    public function setSrcPort(int $src_port): self
    {
        $this->src_port = $src_port;

        return $this;
    }

    public function getDstPort(): ?int
    {
        return $this->dst_port;
    }

    public function setDstPort(int $dst_port): self
    {
        $this->dst_port = $dst_port;

        return $this;
    }

    public function getSentBytes(): ?int
    {
        return $this->sent_bytes;
    }

    public function setSentBytes(int $sent_bytes): self
    {
        $this->sent_bytes = $sent_bytes;

        return $this;
    }

    public function getRecvBytes(): ?int
    {
        return $this->recv_bytes;
    }

    public function setRecvBytes(int $recv_bytes): self
    {
        $this->recv_bytes = $recv_bytes;

        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getExceptions(): ?string
    {
        return $this->exceptions;
    }

    public function setExceptions(?string $exceptions): self
    {
        $this->exceptions = $exceptions;

        return $this;
    }

    public function getActivityname(): ?string
    {
        return $this->activityname;
    }

    public function setActivityname(string $activityname): self
    {
        $this->activityname = $activityname;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->user_agent;
    }

    public function setUserAgent(?string $user_agent): self
    {
        $this->user_agent = $user_agent;

        return $this;
    }

    public function getStatusCode(): ?string
    {
        return $this->status_code;
    }

    public function setStatusCode(?string $status_code): self
    {
        $this->status_code = $status_code;

        return $this;
    }

    public function getTransactionid(): ?string
    {
        return $this->transactionid;
    }

    public function setTransactionid(?string $transactionid): self
    {
        $this->transactionid = $transactionid;

        return $this;
    }

    public function getReferer(): ?string
    {
        return $this->referer;
    }

    public function setReferer(?string $referer): self
    {
        $this->referer = $referer;

        return $this;
    }

    public function getDownloadFileName(): ?string
    {
        return $this->download_file_name;
    }

    public function setDownloadFileName(?string $download_file_name): self
    {
        $this->download_file_name = $download_file_name;

        return $this;
    }

    public function getDownloadFileType(): ?string
    {
        return $this->download_file_type;
    }

    public function setDownloadFileType(?string $download_file_type): self
    {
        $this->download_file_type = $download_file_type;

        return $this;
    }

    public function getUploadFileName(): ?string
    {
        return $this->upload_file_name;
    }

    public function setUploadFileName(?string $upload_file_name): self
    {
        $this->upload_file_name = $upload_file_name;

        return $this;
    }

    public function getUploadFileType(): ?string
    {
        return $this->upload_file_type;
    }

    public function setUploadFileType(?string $upload_file_type): self
    {
        $this->upload_file_type = $upload_file_type;

        return $this;
    }

    public function getConId(): ?string
    {
        return $this->con_id;
    }

    public function setConId(?string $con_id): self
    {
        $this->con_id = $con_id;

        return $this;
    }

    public function getApplication(): ?string
    {
        return $this->application;
    }

    public function setApplication(?string $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getAppIsCloud(): ?bool
    {
        return $this->app_is_cloud;
    }

    public function setAppIsCloud(bool $app_is_cloud): self
    {
        $this->app_is_cloud = $app_is_cloud;

        return $this;
    }

    public function getOverrideName(): ?string
    {
        return $this->override_name;
    }

    public function setOverrideName(?string $override_name): self
    {
        $this->override_name = $override_name;

        return $this;
    }

    public function getOverrideAuthorizer(): ?string
    {
        return $this->override_authorizer;
    }

    public function setOverrideAuthorizer(?string $override_authorizer): self
    {
        $this->override_authorizer = $override_authorizer;

        return $this;
    }

    public function getCategoryType(): ?string
    {
        return $this->category_type;
    }

    public function setCategoryType(?string $category_type): self
    {
        $this->category_type = $category_type;

        return $this;
    }
}
