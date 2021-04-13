import React, {useState} from 'react';
import {Col, Container, Row} from "reactstrap";
import {ToastContainer} from "react-toastify";

export default function CreateProfile () {

    const [state, setState] = useState({
        firstName: "",
        lastName: "",
        birth: "",
        gender: "",
        address: ""
    });

    const handleChange = (e) => {
        const id = e.target.name;
        const value = e.target.type === "checkbox" ? e.target.checked : e.target.value;
        setState(prevState => ({
            ...prevState,
            [id]: value
        }))
    };

    const handleSubmitClick = (e) => {
        e.preventDefault();
        console.log("Clicked");
    };

    return (
        <div>
            <Container>
                <Row>
                    <Col className={"halfColumn col-sm pt-lg-6 pt-md-3 pt-sm-1 pt-0 pr-lg-5 pr-xl-6 justify-content-center align-items-center"}>
                        <div className={" vertical-center"}>
                            <h1 className={"welcomeText"}>Get Ready to Connect</h1>
                            {/*put here upload photo input field*/}
                            <h5 className={"joinText"}>Join MUKA now and find people with the same passion as you!</h5>
                        </div>
                    </Col>
                    <Col className={"halfColumn col-sm pt-lg-6 pt-md-3 pt-sm-1 pt-0 pr-lg-5 pr-xl-6 justify-content-center align-items-center"}>
                        <form>
                            <Row className={"spacedRow"}>
                                <input type="text"
                                       name="firstName"
                                       className="form-control"
                                       value={state.firstName}
                                       onChange={handleChange}
                                       required
                                       placeholder="First Name"/>
                            </Row>
                            <Row className={"spacedRow"}>
                                <input type="text"
                                       name="lastName"
                                       className="form-control"
                                       value={state.lastName}
                                       onChange={handleChange}
                                       required
                                       placeholder="Last Name"/>
                            </Row>
                            <Row className={"spacedRow"}>
                                <input type="text"
                                       name="address"
                                       className="form-control"
                                       value={state.gender}
                                       onChange={handleChange}
                                       required
                                       placeholder="Address"/>
                            </Row>
                            <Row className={"spacedRow"}>
                                <input type="text"
                                       name="birth"
                                       className="form-control"
                                       value={state.birth}
                                       onChange={handleChange}
                                       required
                                       placeholder="Birthday"/>
                            </Row>
                            <Row className={"spacedRow"}>
                                <input type="text"
                                       name="address"
                                       className="form-control"
                                       value={state.address}
                                       onChange={handleChange}
                                       required
                                       placeholder="Address"/>
                            </Row>

                            <Row className={"spacedRow"}>
                                <button className="btn btn-lg login-btn" type="submit" onClick={handleSubmitClick}>
                                    Sign in
                                </button>
                            </Row>
                        </form>
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