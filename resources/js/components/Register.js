import '../../sass/login.scss';
import hugeImg from '/images/huge_muka_logo.png';

import React, {useState} from 'react';
import axios from 'axios';
import {withRouter} from "react-router-dom";
import {Container, Row, Col} from 'reactstrap';

function Register(props) {

    const [state, setState] = useState({
        email: "",
        password: "",
        confirmPassword: "",
        checkedTC: false
    });

    const handleChange = (e) => {
        const id = e.target.name;
        const value = e.target.type === "checkbox" ? e.target.checked : e.target.value;
        setState(prevState => ({
            ...prevState,
            [id]: value
        }))
    };

    const sendDetailsToServer = () => {

        const payload = {
            "email": state.email,
            "password": state.password,
        };

        axios.post(process.env.MIX_APP_BASE_URL + "/register", payload)
            .then(function (response) {
                    switch (response.status) {
                        case 201:
                            console.log("Successful registration.");
                            props.history.push('/register/confirm');
                            break;
                        case 409:
                            console.log("User already exists.");
                            break;
                        case 422:
                            console.log("Invalid credentials");
                            break;
                        case 400:
                        case 500:
                            console.log("Server error.");
                            break;
                        default:
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

        if (state.password !== state.confirmPassword) {
            console.log("Non matching passwords.");
        } else if (state.password.length < 8) {
            console.log("Password length < 8.")
        } else if (!state.checked) {
            console.log("Terms and Conditions not accepted")
        }else{
            sendDetailsToServer();
        }
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
                            <Row className={"spacedRow"}>
                                <input type="password"
                                       name="password"
                                       id="confirmPassword"
                                       className="form-control login-input"
                                       value={state.confirmPassword}
                                       onChange={handleChange}
                                       required
                                       placeholder="Confirm Password"/>
                            </Row>
                            <Row>
                                <input
                                    type="checkbox"
                                    name="checkedTC"
                                    checked={state.checked}
                                    onChange={handleChange}
                                    required
                                />
                                <label htmlFor="checkbox"> Accept the Terms and Conditions</label>
                            </Row>
                            <Row className={"spacedRow"}>
                                <button className="btn btn-lg login-btn" type="submit" onClick={handleSubmitClick}>
                                    Register
                                </button>
                            </Row>
                        </form>
                    </div>
                </Col>
            </Row>
        </Container>
    )
}

export default withRouter(Register);
