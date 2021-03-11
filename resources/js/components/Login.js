import React from 'react';
import ReactDOM from 'react-dom';
import { Container, Row, Col } from 'reactstrap';
import homeImg from '/images/homepage.png';

console.log(homeImg);

function Login() {
    return (
      <Container fluid>
      <image src={homeImg} style={{display:'block', height: 100, width: 100}} alt="Muka Logo"/>
        <Row>
          <Col>
            <div className="card-header">
              <h2>Welcome!</h2>
              <p>Join Muka now to find people with the same passion as you!</p>
            </div>
          </Col>
          <Col>
            <div className="card-header"><h2>Login page!</h2></div>
          </Col>
        </Row>
      </Container>
    );
}

export default Login;
