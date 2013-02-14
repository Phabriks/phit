<?php
/**
* Phit Core files
*
* PHP VERSION 5
*
* @category  Phit
* @package   Phit.Core
* @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
* @copyright 2013 Phabriks
* @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
* @version   SVN: $Id:$
* @link      http://phit.phabriks.fr
*/

namespace Phabriks\Phit;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

/**
* The Compiler class compiles phit into a phar
*
* @category  Phit
* @package   Phit.Core
* @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
* @copyright 2013 Phabriks
*/
class Compiler
{
    protected $rootPath = false;

    /**
     * Compiles phit into a single phar file
     *
     * @param string $pharFile The full path to the file to create
     *
     * @throws \RuntimeException
     */
    public function compile($pharFile = 'phit.phar')
    {
        $this->rootPath = dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR;
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        //$process = new Process('git log --pretty="%h" -n1 HEAD', __DIR__);
        //if ($process->run() != 0) {
        //    throw new \RuntimeException('Can\'t run git log. You must ensure to run compile from composer git repository clone and that git binary is available.');
        //}
        //$this->version = trim($process->getOutput());//
        //$process = new Process('git describe --tags HEAD');
        //if ($process->run() == 0) {
        //    $this->version = trim($process->getOutput());
        //}
        $this->version = '1.0.0-dev';

        $phar = new \Phar($pharFile, 0, 'phit.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->notName('Compiler.php')
            ->in($this->rootPath . 'src');

        foreach ($finder as $file) {
            $this->_addFile($phar, $file);
        }
        //$this->addFile($phar, new \SplFileInfo(__DIR__ . '/Autoload/ClassLoader.php'), false);

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.json')
            ->in($this->rootPath . 'src');

        foreach ($finder as $file) {
            $this->_addFile($phar, $file, false);
        }

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.yml')
            ->in($this->rootPath . 'src');

        foreach ($finder as $file) {
            $this->_addFile($phar, $file, false);
        }

        $this->_addFile($phar, new \SplFileInfo($this->rootPath . 'vendor/composer/composer/src/Composer/IO/hiddeninput.exe'), false);

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->exclude('Tests')
            ->in($this->rootPath . 'vendor/symfony/')
            ->in($this->rootPath . 'vendor/composer/')
            ->in($this->rootPath . 'vendor/seld/jsonlint/src/')
            ->in($this->rootPath . 'vendor/justinrainbow/json-schema/src/')
            ->in($this->rootPath . 'vendor/pdepend')
            ->in($this->rootPath . 'vendor/phpmd')
            ->in($this->rootPath . 'vendor/phpunit')
            ->in($this->rootPath . 'vendor/squizlabs');

        foreach ($finder as $file) {
            $this->_addFile($phar, $file);
        }

        $this->_addFile($phar, new \SplFileInfo($this->rootPath . 'vendor/autoload.php'));
        $this->_addPhitBin($phar);

        // Stubs
        $phar->setStub($this->_getStub());

        $phar->stopBuffering();

        $this->_addFile($phar, new \SplFileInfo($this->rootPath . 'LICENSE'), false);

        unset($phar);
    }

    private function _addFile($phar, $file, $strip = true)
    {
        $path = str_replace($this->rootPath, '', $file->getRealPath());

        $content = file_get_contents($file);
        if ($strip) {
            $content = $this->_stripWhitespace($content);
        } elseif ('LICENSE' === basename($file)) {
            $content = "\n".$content."\n";
        }

        $content = str_replace('@package_version@', $this->version, $content);

        $phar->addFromString($path, $content);
    }

    private function _addPhitBin($phar)
    {
        $content = file_get_contents($this->rootPath . 'bin/phit');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('bin/phit', $content);
    }

    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * @param string $source A PHP string
     *
     * @return string The PHP string with the whitespace removed
     */
    private function _stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }

        return $output;
    }

    private function _getStub()
    {
        $stub = <<<'EOF'
#!/usr/bin/env php
<?php
/**
* Phit Core files
*
* PHP VERSION 5
*
* @category  Phit
* @package   Phit.Core
* @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
* @copyright 2013 Phabriks
* @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
* @version   SVN: $Id:$
* @link      http://phit.phabriks.fr
*/

Phar::mapPhar('phit.phar');

EOF;

        // add warning once the phar is older than 30 days
        if (preg_match('{^[a-f0-9]+$}', $this->version)) {
            $warningTime = time() + 30*86400;
            $stub .= "define('COMPOSER_DEV_WARNING_TIME', $warningTime);\n";
        }

        return $stub . <<<'EOF'
require 'phar://phit.phar/bin/phit';

__HALT_COMPILER();
EOF;
    }
}
