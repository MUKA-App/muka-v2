import '../../sass/login.scss';
import React, {useState} from 'react';
import {Container, Row, Col} from 'reactstrap';
import hugeImg from '/images/huge_muka_logo.png';
import axios from "axios";
import {withRouter} from "react-router-dom";
import {toast, ToastContainer} from "react-toastify";
import 'react-toastify/dist/ReactToastify.css';

function Login(props) {

    const [state, setState] = useState({
        email: "",
        password: "",
        remember: false
    });

    const notify = (text) => toast(text);

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
            "remember_token": state.remember
        };

        axios.post(process.env.MIX_APP_BASE_URL + "/login", payload)
            .then(function (response) {
                    switch (response.status) {
                        case 200:
                            localStorage.setItem('auth', 'true');
                            props.history.push('/dashboard');
                            break;
                        case 401:
                            notify("Incorrect password given!");
                            break;
                        case 422:
                            notify("The password must be at least 8 characters!");
                            break;
                        default:
                            notify("Error occurred with code: " + response.status);
                    }
                }
            )
            .catch(function (error) {
                notify(error.response.data.message);
            });
    };

    const handleSubmitClick = (e) => {
        e.preventDefault();
        if (state.email === "") {
            notify("You must use an email address!");
        } else if (state.password.length < 8) {
            notify("The password must be at least 8 characters!")
        } else {
            sendDetailsToServer();
        }
    };

    return (
        <div>
            <Container className={"footeredContent"}>
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
                                           className="form-control login-input"
                                           value={state.email}
                                           onChange={handleChange}
                                           required
                                           placeholder="Email e.g. john.doe@example.com"/>
                                </Row>
                                <Row className={"spacedRow"}>
                                    <input type="password"
                                           name="password"
                                           className="form-control login-input"
                                           value={state.password}
                                           onChange={handleChange}
                                           required
                                           placeholder="Password"/>
                                </Row>
                                <Row>
                                    <input
                                        type="checkbox"
                                        name="remember"
                                        checked={state.remember}
                                        onChange={handleChange}
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

            <ToastContainer
                position="bottom-right"
                autoClose={5000}
                hideProgressBar={false}
                newestOnTop={false}
                closeOnClick
                rtl={false}
                pauseOnFocusLoss
                draggable
                pauseOnHover
            />

        </div>
    )
}

export default withRouter(Login);
