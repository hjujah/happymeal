<div class="vCenter">
	<div class="vCenterKid">

		<div class="login-panel">
			<div class="panel-body">

				<form method="POST" action="{{{ Confide::checkAction('Vizioart\Cookbook\AuthController@do_login') ?: URL::to('/account/login') }}}" accept-charset="UTF-8">
					<input type="hidden" name="_token" value="{{{ Session::getToken() }}}">


					<div class="input-group">
						<span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
						<input class="form-control" tabindex="1" placeholder="{{{ Lang::get('confide::confide.username_e_mail') }}}" type="text" name="email" id="email" value="{{{ Input::old('email') }}}">
					</div>

					<div class="input-group mbm">
						<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
						<input class="form-control" tabindex="2" placeholder="{{{ Lang::get('confide::confide.password') }}}" type="password" name="password" id="password">
					</div>

					@if ( Session::get('error') )
						<div class="login-msg error">{{{ Session::get('error') }}}</div>
					@endif

					@if ( Session::get('notice') )
						<div class="login-msg notice">{{{ Session::get('notice') }}}</div>
					@endif

					<button tabindex="3" type="submit" class="btn btn-primary btn-block">{{{ Lang::get('confide::confide.login.submit') }}}</button>

					<div class="checkbox">
						<label>
							<input type="hidden" name="remember" value="0">
							<input tabindex="4" type="checkbox" name="remember" id="remember" value="1">
							{{{ Lang::get('confide::confide.login.remember') }}}
						</label>
					</div>

					<div>
						<a href="{{{ (Confide::checkAction('Vizioart\Cookbook\AuthController@forgot_password')) ?: 'forgot' }}}">{{{ Lang::get('confide::confide.login.forgot_password') }}}</a>
					</div>


				</form><!-- .panel -->

			</div>
		</div><!-- .panel -->

	</div>
</div>

