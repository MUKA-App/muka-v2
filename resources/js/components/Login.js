import '../../sass/app.scss';
import '../../sass/login.scss';
import React from 'react';
import { Container, Row, Col } from 'reactstrap';
import hugeImg from '/images/huge_muka_logo.png';

export default function Login () {
    return (
        <Container>
          <Row>
            <Col className={"halfColumn col-sm pt-lg-6 pt-md-3 pt-sm-1 pt-0 pr-lg-5 pr-xl-6 justify-content-center align-items-center"}>
              <div className={" vertical-center"}>
                <h1 className={"welcomeText"}>Welcome</h1>
                <img className={"loginPhoto"} src={hugeImg}/>
                <h5 className={"joinText"}>Join MUKA now and find people with the same passion as you!</h5>
              </div>
            </Col>
            <Col>
              <div className={"vertical-center"}>
                <form>
                  <Row className={"spacedRow"}>
                      <input type="email" name="email" id="inputEmail" class="form-control login-input" required placeholder="Email e.g. john.doe@example.com"/>
                  </Row>
                  <Row className={"spacedRow"}>
                      <input type="password" name="password" id="inputPassword" class="form-control login-input" required placeholder="Password"/>
                  </Row>
                  <Row className={"spacedRow"}>
                    <button class="btn btn-lg login-btn" type="submit">
                        Sign in
                    </button>
                  </Row>
                </form>
              </div>
            </Col>
          </Row>
        </Container>
    )
}

/*
<div class="container content">
    <div class="row">
        <div class="col-sm pt-lg-6 pt-md-3 pt-sm-1 pt-0 pr-lg-5 pr-xl-6 justify-content-center align-items-center">
            <div>
                <h1 class="display-1" style="text-align: center">Welcome</h1>
                <br>
                <h5 class="h5" style="text-align: center">Register MUKA now and find people with the same passion as you!</h5>
            </div>

            <center><img src="{{ asset('images/homepage.png') }}" style="height: 250px" alt="Music is life"></center>
            <br>
        </div>
        <div class="col-sm pt-lg-8 pt-md-3 pt-sm-1 pt-0 pl-lg-5 pl-xl-6 justify-content-center align-items-center text-center">
            <div class="row">
                <div class="col-sm">
                    <form method="post">
                        {% if error %}
                            <div class="alert alert-danger">{{ error.messageKey|trans(error.messageData, 'security') }}</div>
                        {% endif %}

                        {% if app.user %}
                            <div class="mb-3">
                                You are logged in as {{ app.user.username }}, <a href="{{ path('app_logout') }}">Logout</a>
                            </div>
                        {% endif %}

                        <h1 class="display-4 mb-3 font-weight-normal">Log in</h1>
                        <div class="form-group">
                            <input type="email" value="{{ last_username }}" name="email" id="inputEmail" class="form-control login-input" required placeholder="Email e.g. john.doe@example.com">
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" id="inputPassword" class="form-control login-input" required placeholder="Password">
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="_csrf_token"
                                   value="{{ csrf_token('authenticate') }}"
                            >
                        </div>


                        <button class="btn btn-lg btn-primary login-btn" type="submit">
                            Sign in
                        </button>
                    </form>
                </div>
            </div>
            <div class="row pt-2">
                <div class="col-sm">
                    <a class="link" href="/forgotten-password">Forgotten password?</a>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    Not a member?<a class="link" href="/register">Register us here!</a>
                </div>
            </div>
        </div>
    </div>
</div>
*/
