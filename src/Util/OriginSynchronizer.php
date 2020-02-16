<?php

namespace App\Util;

use App\Entity\Origin;

/**
 * \brief     An assitant class of the retriever, syncs the origins data.
 *
 *
 */
class OriginSynchronizer
{
    /**
     * Retrieves the origin's data, and syncs it with the DB.
     *
     * 
     */
    public function syncOrigins($localOrigins, $remoteOrigins)
    {

        if (empty($remoteOrigins) && empty($localOrigins)) {
            return [
                'entities' => [],
                'summary' => [
                    'new_origins' => 0,
                    'modified_origins' =>0,
                    'active_origins'=>0,
                    'inactive_origins' =>0,
                    'total_origins' =>0]
            ]; 
        }
        if (empty($remoteOrigins)) {
            return $this->deactivateAllOrigins($localOrigins);
        }
        if (empty($localOrigins)) {
            return $this->fillLocalOrigins($remoteOrigins);
        }
        return $this->getOriginsChanges($localOrigins, $remoteOrigins);

    }

    protected function makeOrigin($subnet, $name, $active)
    {
        $origin = new Origin();
        $origin->setSubnet($subnet);
        $origin->setName($name);
        $origin->setActive($active);
        return $origin;
    }

    protected function deactivateAllOrigins( $localOrigins)
    {
        $outputOrigins = [];
        $modifiedOrigins = 0;
        $inactiveOrigins = 0;
        $totalOrigins = count($localOrigins);
        foreach ($localOrigins as $localO) {
            if ($localO->getActive()) {
                $localO->setActive(false);
                $modifiedOrigins +=1;
            }
            $outputOrigins[]= $localO;
            $inactiveOrigins +=1;
        }
        return [
            'entities' => $outputOrigins,
            'summary' => [
                'new_origins' => 0,
                'modified_origins' => $modifiedOrigins,
                'active_origins'=>0,
                'inactive_origins' => $inactiveOrigins,
                'total_origins' =>$totalOrigins
            ]
        ];
    }

    protected function fillLocalOrigins($remoteOrigins)
    {
        $outputOrigins = [];
        $newOrigins = 0;
        foreach ($remoteOrigins as $remoteO) {
            if ($remoteO['red']) {
                $newOrigin = $this->makeOrigin(
                    $remoteO["subnet"],
                    $remoteO["nomeSede"],
                    true
                );
                $newOrigins +=1;
                $outputOrigins[]= $newOrigin;
            }
        }
        return [
            'entities' => $outputOrigins,
            'summary' => [
                'new_origins' => $newOrigins,
                'modified_origins' => 0,
                'active_origins'=>$newOrigins,
                'inactive_origins' => 0,
                'total_origins' =>$newOrigins
            ]
        ];
    }

    protected function getOriginsChanges($localOrigins, $remoteOrigins)
    {
        $outputOrigins = [];
        $modifiedOrigins = 0;
        $inactiveOrigins = 0;
        $activeOrigins =0;
        $newOrigins = 0;
        $totalOrigins =0 ;
        foreach ($remoteOrigins as $remoteO) {
            $isNewRemote = true;
            foreach ($localOrigins as $localO) {
                if ($remoteO["subnet"] === $localO->getSubnet())
                {
                    $totalOrigins += 1;
                    $isNewRemote = false;
                    $modified = false;
                    if ($localO->getActive()) {
                        $activeOrigins +=1;
                    }
                    if (!$localO->getActive()) {
                        $inactiveOrigins +=1;
                    }
                    if ($remoteO["nomeSede"] !== $localO->getName()) {
                        $localO->setName($remoteO["nomeSede"]);
                        $modified = true;
                    }
                    if ($remoteO["red"] != $localO->getActive()) {
                        $localO->setActive($remoteO["red"]);
                        $modified = true;
                        $newActive = -1;
                        $newInactive = 1;
                        if ($remoteO["red"]) {
                            $newActive = 1;
                            $newInactive = -1;
                        }
                        $inactiveOrigins += $newInactive;
                        $activeOrigins += $newActive;            
                    }
                    if ($modified) {
                        $outputOrigins[]= $localO;
                        $modifiedOrigins +=1;
                    }
                }
            }
            if ($isNewRemote) {
                if ($remoteO["red"]) {
                    $totalOrigins += 1;
                    $newOrigin = new Origin();
                    $newOrigin->setName($remoteO["nomeSede"]);
                    $newOrigin->setSubnet($remoteO["subnet"]);
                    $newOrigin->setActive(true);
                    $newOrigins +=1;
                    $activeOrigins +=1;
                    $outputOrigins[]= $newOrigin;
                }
            }
        }
        
        return [
            'entities' => $outputOrigins,
            'summary' => [
                'new_origins' => $newOrigins,
                'modified_origins' => $modifiedOrigins,
                'active_origins'=> $activeOrigins,
                'inactive_origins' => $inactiveOrigins,
                'total_origins' =>$totalOrigins
            ]
        ];
    }
}
