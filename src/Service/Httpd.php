<?php

namespace ContainerBuilder\Service;

class Httpd extends AbstractService
{
    protected $versions = ['php:5.6-apache', 'php:7.0-apache', 'php:7.1-apache'];
    protected $config = [
        'volumes' => [],
        'services' => [
            'httpd' => [
                'build' => './docker/httpd',
            ]
        ]
    ];

    protected $files = [
        __DIR__ . '/../../data/templates/httpd/Dockerfile' => 'docker/httpd/Dockerfile',
    ];

    protected $serviceName = 'httpd';

    /**
     * Mapping of extensions we support and how we should install them
     * @var array
     */
     protected $extensions = [
        'pecl' => ['xdebug'],
        'stock' => [
            'cmath', 'bz2', 'calendar', 'ctype', 'curl', 'dba', 'dom', 'enchant', 'exif', 'fileinfo', 'filter', 'ftp',
            'gd', 'gettext', 'gmp', 'hash', 'iconv', 'imap', 'interbase', 'intl', 'json', 'ldap', 'mbstring', 'mcrypt',
            'mysqli', 'oci8', 'odbc', 'opcache', 'pcntl', 'pdo', 'pdo_dblib', 'pdo_firebird', 'pdo_mysql', 'pdo_oci',
            'pdo_odbc', 'pdo_pgsql', 'pdo_sqlite', 'pgsql', 'phar', 'posix', 'pspell', 'readline', 'recode',
            'reflection', 'session', 'shmop', 'simplexml', 'snmp', 'soap', 'sockets', 'spl', 'standard', 'sysvmsg',
            'sysvsem', 'sysvshm', 'tidy', 'tokenizer', 'wddx', 'xml', 'xmlreader', 'xmlrpc', 'xmlwriter', 'xsl', 'zip'
        ],
    ];

    public function getFiles()
    {
        $files = parent::getFiles();

        $extensions = 'true';
        if (isset($this->overrides['build-options'])) {
            if (isset($this->overrides['build-options']['extensions'])) {
                $stockExtensions = array_intersect($this->overrides['build-options']['extensions'], $this->extensions['stock']);
                $peclExtensions = array_intersect($this->overrides['build-options']['extensions'], $this->extensions['pecl']);

                $stockString = 'docker-php-ext-install ' . implode(' ', $stockExtensions);
                $peclString = 'pecl install ' . implode(' ', $peclExtensions);

                $extensions = '';
                if (count($stockExtensions)) { $extensions .= $stockString; }
                if (count($peclExtensions)) {
                    $extensions .= (strlen($extensions) == 0) ? $peclString : ' && ' . $peclString;
                }
            }
        }

        $files['docker/httpd/Dockerfile'] = str_replace('{{ extensions }}', $extensions, $files['docker/httpd/Dockerfile']);
        
        return $files;
    }
}