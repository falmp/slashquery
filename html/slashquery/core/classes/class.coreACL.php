<?php
/**
 * coreACL - /slashquery/core/classes/class.coreACL.php
 *
 * Access Control List
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class coreACL extends sqBase {

  /**
	 * start the object
	 *
	 * @param object $sqRouter
	 * @param closure $DB
	 */
  public function __construct(sqRouter $sqRouter, Closure $DB) {
    $this->router = $sqRouter;

		/**
     * @var DALMP
     */
    $this->DB = $DB;

		/**
     *@var int used when listing the permissions
     */
    $this->roleID = sqTools::postVars('rid') ? $_POST['rid'] : 0;

    /**
     * find new modules
     */
    $this->findModules();
  }

  /**
	 * findModules sqSite/modules/module/module_module.php
	 *
	 * @access protected
	 */
	protected function findModules() {
    $this->cleanModules();
    $dirs = array('cpanel' => SQ_ROOT . 'slashquery/core/modules',
									'site' => SQ_ROOT . 'slashquery/sites/'.$this->router->site.'/modules',
									'cpanelExt' => SQ_ROOT . 'slashquery/sites/'.$this->router->site.'/cpanel');
    $modules = array();
    foreach ($dirs as $key => $dir) {
      $files = scandir($dir);
      foreach ($files as $data) {
        if (!preg_match('/^\./', $data)) {
          $inc = "$dir/$data/$data" . '_module.php';
          if (file_exists($inc)) {

						/**
						 * to avoid overlapping the original key (cpanelExt)
						 */
						$nKey = $key;

						if ($key == 'cpanel') {
							/**
							 * check for global cpanel extensions
							 */
							if (!in_array($data, array('users', 'ACL', 'configuration', 'cpanel'))) {
								/**
								 * check if module_sites.php exists, if no file found, module
								 * will be available on all sites.
								 */
								$inc_sites = "$dir/$data/$data" . '_sites.php';
								if (file_exists($inc_sites)) {
									require $inc_sites;
									if (!in_array($this->router->site, $allowed_sites)) {
										/**
										 * skip module since it is not allowed for this site
										 * break the foreach loop
										 */
										continue;
									}

									/**
									 * global extensions have id = 2
									 */
									$modules[$nKey][$data]['type'] = 2;
								}
							} else {
								$modules[$nKey][$data]['type'] = 1;
							}
						} elseif ($key == 'cpanelExt') {
							$nKey = 'cpanel';
							$modules[$nKey][$data]['type'] = 3;
						}

						/**
						 * get module information
						 */
						require_once $inc;
						asort($module_ACL);
						$modules[$nKey][$data]['name'] = $data;
						$modules[$nKey][$data]['description'] = trim($module_description);
						$modules[$nKey][$data]['ACL'] = $module_ACL;
						unset($module_description);
						unset($module_ACL);
					}
        }
      }
    }

		/**
		 * set modules so that other methods can see them
		 */
		$this->modules = $modules;

    $cpanel_modules = ($rs = $this->DB()->getASSOC('SELECT name, description FROM sq_modules WHERE cpanel > 0')) ? $rs : array();
    $site_modules = ($rs = $this->DB()->getASSOC('SELECT name, description FROM sq_modules WHERE cpanel = 0')) ? $rs : array();

    $admin_ACL = array();
    foreach ($modules as $type => $values) {
      foreach ($values as $module => $value) {
        $admin_ACL[$type][$module] = $value['ACL'];

        switch ($type) {
          case 'cpanel':
            if (!array_key_exists($module, $cpanel_modules) || $cpanel_modules[$module] != $value['description']) { // insert new modules to database
              $this->DB()->PExecute('REPLACE INTO sq_modules (name,description,cpanel,status) VALUES(?,?,?,1)', $module, $value['description'], $value['type']);
            }
            break;

          case 'site':
            if (!array_key_exists($module, $site_modules) || $site_modules[$module] != $value['description']) { // insert new modules to database
              $rs = $this->DB()->PExecute('REPLACE INTO sq_modules (name,description,cpanel) VALUES(?,?,0)', $module, $value['description']);
            }
            break;
        }
      }
    }

    $admin_ACL = json_encode($admin_ACL);

    $rs = $this->DB()->PgetOne("SELECT ACL = ? FROM sq_ACLs WHERE rid=3", $admin_ACL);
    if (!$rs) {
      $rs = $this->DB()->PExecute('REPLACE INTO sq_ACLs (rid,ACL) VALUES(?,?)', 3, $admin_ACL);
      if ($rs) {
        $this->DB()->CacheFlush('group:ACL');
      }
    }
  }

  /**
	 * cleanModules - remove deleted modules from DB
	 *
	 * @access protected
	 */
  protected function cleanModules() {
		/**
		 * based on the type we check on diferent paths
		 * cpanel 1 = core
		 * cpane1 2 = global extension in core
		 * cpanel 3 = site extension
     */
    if ($modules = $this->DB()->FetchMode('ASSOC')->getAll('SELECT id, name, cpanel FROM sq_modules')) {
      $erase = array();
      foreach ($modules as $module) {
				switch ($module['cpanel']) {
					case 1:
					case 2:
						$mdir = SQ_ROOT . "slashquery/core/modules/$module[name]";
						break;

					case 3:
						$mdir = SQ_ROOT . "slashquery/sites/".$this->router->site."/cpanel/$module[name]";
						break;

					default:
						$mdir = SQ_ROOT . "slashquery/sites/".$this->router->site."/modules/$module[name]";
				}

        if (is_dir($mdir)) {
          $inc = "$mdir/$module[name]" . '_module.php';
          if (!file_exists($inc)) {
            $erase[] = $module['id'];
          } else {
						/**
						 * check if cpanel extension is allowed or not to this site
						 */
						if ($module['cpanel'] && !in_array($module['name'], array('users', 'ACL', 'configuration', 'cpanel'))) {
							/**
							 * check if module_sites.php exists, if no file found, module
							 * will be available on all sites.
							 */
							$inc_sites = "$mdir/$module[name]" . '_sites.php';
							if (file_exists($inc_sites)) {
								$allowed_sites = array();
								require $inc_sites;
								if (!in_array($this->router->site, $allowed_sites)) {
									/**
									 * erase module since it is not allowed for this site
									 */
									$erase[] = $module['id'];
								}
							}
						}
					}
        } else {
          $erase[] = $module['id'];
        }
      }
      if (!empty($erase)) {
        foreach ($erase as $id) {
          $this->DB()->Execute("DELETE FROM sq_modules WHERE id=$id");
        }
        $this->DB()->CacheFlush('group:ACL');
      }
    }
    return true;
  }

  /**
	 * getCpanelModules
	 *
	 * @return array cpanel modules
	 */
  public function getCpanelModules($extensions=false) {
		if ($extensions) {
			return $this->DB()->GetALL("SELECT id, name, description, cpanel, status FROM sq_modules WHERE cpanel > 0 AND name NOT IN('users','cpanel','configuration','ACL') ORDER BY name");
		} else {
			return $this->DB()->GetALL("SELECT id, name, description, status FROM sq_modules WHERE cpanel > 0 ORDER BY FIELD(name,'users','cpanel','configuration','ACL') DESC, name");
		}
  }

  /**
	 * getSiteModules
	 *
	 * @return array sites modules
	 */
	public function getSiteModules() {
    return $this->DB()->GetAll('SELECT id, name, description, status FROM sq_modules WHERE cpanel=0 ORDER BY name');
  }

  /**
   * get ACL depending on the role
   *
   * @return array ACL
   */
  public function getACLs() {
    $this->DB()->FetchMode('ASSOC');
    $rs = $this->roleID ? $this->DB()->PGetASSOC('SELECT rid, ACL FROM sq_ACLs WHERE rid = ?', $this->roleID) : $this->DB()->GetASSOC('SELECT rid, ACL FROM sq_ACLs WHERE rid < 3');
    $cmodules = array();
    if ($rs) {
      foreach ($rs as $rids => $value) {
        $ACLs = json_decode($value, true);
				/**
				 * updates ACL if a module is updated
				 */
        if (!empty($ACLs)) {
          foreach ($ACLs as $type => $values) {
            $cACLs[$rids][$type] = $values;
            foreach ($values as $module => $perms) {
              foreach ($perms as $key => $perm) {
								/* check current modules (file system) vs DB stored modules */
								if (isset($this->modules[$type][$module]['ACL'][$key])) {
									$cmodules[$type][$module][$key] = $this->modules[$type][$module]['ACL'][$key];
								}
              }
            }
						/* if curent modules != stored modules, update DB with cmodules */
            if ($cmodules != $ACLs) {
              if ($this->DB()->PExecute('REPLACE INTO sq_ACLs (rid,ACL) VALUES(?,?)', $rids, json_encode($cmodules))) {
                unset($cmodules);
                $this->DB()->CacheFlush('group:ACL');
              }
            }
          }
        }
			}
    }
		return isset($cACLs) ? $cACLs : array();
  }

  /**
	 * getRoles
	 *
	 * @param int level
	 * 1 - anonymous users
   * 2 - authenticaed users
   * 3 - administrators
	 * @return array roles
	 */
  public function getRoles($level=3) {
    $roles = array();
    if ($rs = $this->DB()->FetchMode('ASSOC')->PgetALL('SELECT * FROM sq_roles WHERE rid > ? ORDER BY name', $level)) {
      foreach ($rs as $value) {
        $roles[$value['rid']] = htmlentities($value['name'], ENT_QUOTES | ENT_IGNORE, 'UTF-8');
      }
      return $roles;
    } else {
      return $roles;
    }
  }

  /**
	 * update Default roles
	 *
	 * @param $_POST
	 */
	public function updateRoles() {
    if (sqSession::validToken($_POST['token'], 'token')) {
    	$modules = $this->modules;
    	$process = function($data) use ($modules) {
    		$permissions = array();
        foreach ($data as $type => $values) {
          foreach ($values as $module => $value) {
            foreach ($value as $perm) {
              $permissions[$type][$module][$perm] = $modules[$type][$module]['ACL'][$perm];
            }
          }
        }
        return json_encode($permissions);
    	};

    	switch (true) {
    		case isset($_POST['rid']):
    		  $this->roleID = $_POST['rid'];
    			$data = isset($_POST['permissions'][$_POST['rid']]) ? $process($_POST['permissions'][$_POST['rid']]) : '';
    			$this->DB()->PExecute('REPLACE INTO sq_ACLs (rid,ACL) VALUES(?,?)', $_POST['rid'], $data);
    			break;

    		default :
    			for ($i = 1; $i < 3; $i++) {
    				$data = isset($_POST['permissions'][$i]) ? $process($_POST['permissions'][$i]) : '';
    				$this->DB()->PExecute('REPLACE INTO sq_ACLs (rid,ACL) VALUES(?,?)', $i, $data);
    			}
    	}
    	$this->DB()->CacheFlush('group:ACL');
    } else {
      return false;
    }
	}

  /**
	 * toggle the module status
	 * modules with id 1,2,3,4 always are on (cpanel,ACL,configuration,users)
	 * @param int $mid module id
	 * @return bool module status
	 */
  public function moduleStatus($mid) {
    if ($rs = $this->DB()->PExecute('UPDATE sq_modules SET status=1-status WHERE id=? AND id NOT IN (1,2,3,4)', $mid)) {
      $this->DB()->CacheFlush('group:ACL');
      return $rs;
    } else {
      return false;
    }
  }

	/**
	 * AddRole
	 *
	 * @param string $role
	 * @param int $id
	 * @return boolean or int (last insert ID) when creating new role
	 */
	public function addRole($role,$rid) {
		if (empty($role)) {
			return false;
		} else {
      /**
       * if rid > 0 update the role name
       */
			if ($rid > 0) {
        return $this->DB()->PExecute('UPDATE sq_roles SET name=? WHERE rid=?', $role, $rid);
      } else {
        return $this->DB()->PExecute('INSERT INTO sq_roles SET name=?', $role) ? $this->DB()->Insert_id() : false;
      }
		}
	}

  /**
	 * delRole
	 *
	 * @param int $role
	 * @param string $token
	 * @return boolean
	 */
	public function delRole($role, $token) {
		if (sqSession::validToken($token, 'token', false)) {
			return (empty($role)) ? false : $this->DB()->PExecute('DELETE FROM sq_roles WHERE rid=? AND rid NOT IN (1,2,3)', $role);
		} else {
			return false;
		}
	}

}
