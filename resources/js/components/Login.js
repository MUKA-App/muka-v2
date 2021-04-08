import '../../sass/app.scss';
import '../../sass/login.scss';
import React, {useState} from 'react';
import {Container, Row, Col} from 'reactstrap';
import hugeImg from '/images/huge_muka_logo.png';
import axios from "axios";
import {withRouter} from "react-router-dom";

function Login(props) {

    const [state, setState] = useState({
        email: "",
        password: "",
        remember: false
    });

    const handleChange = (e) => {
        const {id, value} = e.target;
        setState(prevState => ({
            ...prevState,
            [id]: value
        }))
    };
    const toggle = (e) => {
        setState(prevState => ({
            ...prevState,
            remember: !prevState.remember
        }))
    };

    const sendDetailsToServer = () => {

        const payload = {
            "email": state.email,
            "password": state.password,
            "remember_token": true
        };

        axios.post(process.env.MIX_APP_BASE_URL + "/login", payload)
            .then(function (response) {
                    if (response.status === 200) {
                        console.log("Successful login.");
                        localStorage.setItem('auth', 'true');
                        props.history.push('/dashboard');
                    } else {
                        console.log("Other error occurred with code: " + response.status);
                    }
                }
            )
            .catch(function (error) {
                console.log(error);
            });
    };

    const handleSubmitClick = (e) => {
        e.preventDefault();
        // TODO: Some validation to be added
        sendDetailsToServer();
    };

    return (
        <Container>
            <Row>
                <Col
                    className={"halfColumn col-sm pt-lg-6 pt-md-3 pt-sm-1 pt-0 pr-lg-5 pr-xl-6 justify-content-center align-items-center"}>
                    <div className={" vertical-center"}>
                        <h1 className={"welcomeText"}>Welcome</h1>
                        <img className={"loginPhoto"} src={hugeImg}/>
                        <h5 className={"joinText"}>Join MUKA now and find people with the same passion as you!</h5>
                    </div>
                </Col>
                <Col>
                    <div className={"bottomdiv"}>
                        <form>
                            <Row className={"spacedRow"}>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       className="form-control login-input"
                                       value={state.email}
                                       onChange={handleChange}
                                       required
                                       placeholder="Email e.g. john.doe@example.com"/>
                            </Row>
                            <Row className={"spacedRow"}>
                                <input type="password"
                                       name="password"
                                       id="password"
                                       className="form-control login-input"
                                       value={state.password}
                                       onChange={handleChange}
                                       required
                                       placeholder="Password"/>
                            </Row>
                            <Row>
                            <input
                                name="checkbox"
                                type="checkbox"
                                checked={state.remember}
                                onChange={toggle}
                            />
                                <label htmlFor="checkbox"> I want to stay logged in</label>
                            </Row>
                            <Row className={"spacedRow"}>
                                <button className="btn btn-lg login-btn" type="submit" onClick={handleSubmitClick}>
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

export default withRouter(Login);