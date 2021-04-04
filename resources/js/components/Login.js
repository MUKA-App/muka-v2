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
                      <input type="email" name="email" id="inputEmail" className="form-control login-input" required placeholder="Email e.g. john.doe@example.com"/>
                  </Row>
                  <Row className={"spacedRow"}>
                      <input type="password" name="password" id="inputPassword" className="form-control login-input" required placeholder="Password"/>
                  </Row>
                  <Row className={"spacedRow"}>
                    <button className="btn btn-lg login-btn" type="submit">
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
