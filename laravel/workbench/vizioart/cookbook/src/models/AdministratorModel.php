<?php namespace Vizioart\Cookbook\Models;



use Zizaco\Confide\ConfideUser;

class AdministratorModel extends ConfideUser {

	protected $table = 'cb_administrators';

	/**
     * Ardent validation rules
     *
     * @var array
     */
    public static $rules = array(
        'username' => 'required|alpha_dash|unique:cb_administrators',
        'email' => 'required|email|unique:cb_administrators',
        'password' => 'required|min:4|confirmed',
        'password_confirmation' => 'min:4',
    );

	/**
     * Create a new AdministratorModel instance.
     */
    public function __construct( array $attributes = array() )
    {
        parent::__construct( $attributes );

        if ( ! static::$app )
            static::$app = app();

        $this->table = 'cb_administrators';
    }

}