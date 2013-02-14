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

namespace Phabriks\Phit\ProjectModel;

/**
 * Phit Base Project Model class
 *
 * @category  Phit
 * @package   Phit.Core
 * @author    Guillaume Maïssa <guillaume.maissa@phabriks.com>
 * @copyright 2013 Phabriks
 */
abstract class BaseModel
{
    /**
     * PHP CodeSniffer standard to use for checkstyle
     * @var string
     */
    protected $phpcsStandard = "PSR-2";

    /**
     * Model requirements to be set in composer.json file
     * @var array
     */
    protected $composerRequirements = array();

    /**
     * VCS type for Project model sources repository
     * @var string
     */
    protected $vcs = 'svn';

    /**
     * Repository for Project model sources
     * @var string
     */
    protected $modelRepo;

    /**
     * Path to Project model sources stable branch on repository
     * @var string
     */
    protected $stableBranch = '/branches/stable';

    /**
     * Path to Project model sources dev branch (trunk) on repository
     * @var string
     */
    protected $devBranch = '/trunk';

    /**
     * Retrieve project model sources repository
     *
     * @param  boolean $devMode is development mode enabled
     *
     * @return string project model sources repository
     */
    public function getRepo($devMode = false)
    {
        if ($devMode) {
            $repo = $this->modelRepo . $this->devBranch;
        } else {
            $repo = $this->modelRepo . $this->stableBranch;
        }

        return $repo;
    }
}
