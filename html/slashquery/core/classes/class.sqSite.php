<?php
/**
 * sqSite - /slashquery/core/classes/class.sqSite.php
 *
 * @author Nicolas Embriz <nbari@slashquery.com>
 * @package SlashQuery
 * @license BSD, see LICENSE file
 * @version 1.0
 */

class sqSite extends sqDI {

  public function __construct(sqRouter $sqRouter) {
    /**
     * helps to identify when page is served from cache or by SlashQuery
     * when serve from cache, the X-Powered-By header will not be set.
     */
    header('X-Powered-By: SlashQuery /?');

    /**
     * sqRouter
     *
     * @var sqRouter $sqRouter
     */
    $this->router = $sqRouter;

    /**
     * $this class object assigned to $self
     * $self = $this;
     * php < 5.4 ex: function () use ($self) {$self->router}
     */

    /**
     * sqACL
     *
     * @return sqACL closure
     */
    $this->c['ACL'] = $this->share(function () {
      return new sqACL($this->router, $this->DB);
    });

    /**
     * sqTPL template
     *
     * @return sqTPL closure
     */
    $this->c['TPL'] = $this->share(function() {
      return new sqTPL($this->router, $this->ACL);
    });

    /**
     * DB - DALMP
     *
     * @return DALMP shared closure
     */
    $this->c['DB'] = $this->share(function () {
      return defined('DB_SSL') ? new DALMP(DSN, json_decode(DB_SSL, 1)) :  new DALMP(DSN);
    });

    /**
     * Config
     *
     * @return string
     */
    $this->c['Config'] = function ($value) {
      return $this->DB()->PgetOne('SELECT config_value FROM sq_config WHERE config_name=?', $value);
    };

  }

}
