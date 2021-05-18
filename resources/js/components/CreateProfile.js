import '../../sass/login.scss';
import React, {useState, useEffect} from 'react';
import {Col, Container, Row} from "reactstrap";
import {toast, ToastContainer} from "react-toastify";
import axios from "axios";
import {withRouter} from "react-router-dom";
import locations from '../data/locations.json';
import instruments from '../data/instruments.json';

function CreateProfile(props) {

    // redirect to dashboard if user has profile
    useEffect(() => {
        axios.get(process.env.MIX_APP_BASE_URL + "/api/profile")
            .then(response => {
                props.history.push('/dashboard');
            })
    }, []);

    const [state, setState] = useState({
        pageNo: 1,
        firstName: "",
        lastName: "",
        birth: "",
        gender: "",
        country: "",
        city: "",
        institution: "",
        instruments: "",
        bio: ""
    });

    const notify = (text) => toast(text);

    const handleChange = (e) => {
        const id = e.target.name;
        let value = "";
        if (e.target.type === "checkbox") {
            value = e.target.checked;
        } else if (e.target.type === "select-multiple") {
            value = Array.from(e.target.selectedOptions, option => option.value);
        } else if (e.target.type === "file") {
            value = e.target.files[0];
        } else {
            value = e.target.value;
        }
        setState(prevState => ({
            ...prevState,
            [id]: value
        }));
    };

    const handleSubmitClick = (e) => {
        e.preventDefault();
        const payload = {
            "first_name": state.firstName,
            "last_name": state.lastName,
            "bio": state.bio,
            "gender": state.gender,
            "country": locations.filter(loc => loc.city === state.city)[0].country_code,
            "city": state.city,
            "instruments": state.instruments,
            "institution": state.institution,
            "birth_date": state.birth
        };
        if (
            state.institution === "" ||
            state.instruments === "" ||
            state.bio === "") {
            notify("You have to complete all fields");
        } else {
            axios.post(process.env.MIX_APP_BASE_URL + "/api/profile", payload)
                .then(response => {
                    if (response.status === 201) {
                        props.history.push('/dashboard');
                    } else {
                        console.log(response);
                    }
                })
                .catch(error => {
                    console.log(error.response);
                    // Object.values(error.response.errors).forEach(e => notify(e.join(',')));
                });
        }
    };

    const changePage = (e) => {
        e.preventDefault();
        if (state.firstName === "" ||
            state.lastName === "" ||
            state.birth === "" ||
            state.gender === "" ||
            state.country === "" ||
            state.city === "") {
            notify("You have to complete all fields");
        } else {
            setState(prevState => ({
                ...prevState,
                pageNo: state.pageNo === 1 ? 2 : 1
            }))
        }
    };

    const uniqueLocations = [...new Set(locations.map(loc => loc.country))];

    return (
        <div>
            {state.pageNo === 1 ?
                <Container>
                    <Row>
                        <Col
                            className={"halfColumn col-sm pt-lg-6 pt-md-3 pt-sm-1 pt-0 pr-lg-5 pr-xl-6 justify-content-center align-items-center"}>
                            <Row className={"spacedRow"}>
                                <h1 className={"welcomeText"}>Sign up to Connect</h1>
                                {/*Photo input goes here*/}
                                <h5 className={"joinText"}>Photo with your instrument is highly recommended :)</h5>
                            </Row>
                        </Col>
                        <Col
                            className={"halfColumn col-sm pt-lg-6 pt-md-3 pt-sm-1 pt-0 pr-lg-5 pr-xl-6 justify-content-center align-items-center"}>
                            <form>
                                <Row className={"spacedRow"}>
                                    <input type="text"
                                           name="firstName"
                                           className="form-control login-input"
                                           value={state.firstName}
                                           onChange={handleChange}
                                           required
                                           placeholder="First Name"/>
                                </Row>
                                <Row className={"spacedRow"}>
                                    <input type="text"
                                           name="lastName"
                                           className="form-control login-input"
                                           value={state.lastName}
                                           onChange={handleChange}
                                           required
                                           placeholder="Last Name"/>
                                </Row>
                                <Row className={"spacedRow"}>
                                    <select name="country" onChange={handleChange} className="form-control login-input">
                                        <option value="country" defaultValue hidden>Country</option>
                                        {uniqueLocations.map((item) =>
                                            <option key={item} value={item}>{item}</option>
                                        )}
                                    </select>

                                </Row>
                                <Row className={"spacedRow"}>
                                    <select name="city" onChange={handleChange} className="form-control login-input">
                                        <option value="city" defaultValue hidden>City</option>
                                        {locations.filter(loc => loc.country === state.country).map((item) =>
                                            <option key={item.city} value={item.city}>{item.city}</option>
                                        )}
                                    </select>
                                </Row>
                                <Row className={"spacedRow"}>
                                    <input type="date"
                                           name="birth"
                                           className="form-control login-input"
                                           value={state.birth}
                                           onChange={handleChange}
                                           required
                                           placeholder="Birthday"/>
                                </Row>
                                <Row className={"spacedRow"}>
                                    <select name="gender" onChange={handleChange} className="form-control login-input">
                                        <option value="gender" defaultValue hidden>Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Non-Binary/Other">Non-Binary/Other</option>
                                        <option value="Rather not say">Rather not say</option>
                                    </select>
                                </Row>

                                <Row className={"spacedRow"}>
                                    <button className="btn btn-lg login-btn" type="submit" onClick={changePage}>
                                        Next Page
                                    </button>
                                </Row>
                            </form>
                        </Col>
                    </Row>
                </Container>
                :
                <Container>
                    <Row>
                        <Col
                            className={"halfColumn col-sm pt-lg-6 pt-md-3 pt-sm-1 pt-0 pr-lg-5 pr-xl-6 justify-content-center align-items-center"}>
                            <Row className={"spacedRow"}>
                                <h1 className={"welcomeText"}>Sign up to Connect</h1>
                                {/*Photo input goes here*/}
                                <h5 className={"joinText"}>Photo with your instrument is highly recommended :)</h5>
                            </Row>
                        </Col>
                        <Col
                            className={"halfColumn col-sm pt-lg-6 pt-md-3 pt-sm-1 pt-0 pr-lg-5 pr-xl-6 justify-content-center align-items-center"}>
                            <form>
                                <Row className={"spacedRow"}>
                                    <input type="text"
                                           name="institution"
                                           className="form-control login-input"
                                           value={state.institution}
                                           onChange={handleChange}
                                           required
                                           placeholder="Institution"/>
                                </Row>
                                <Row className={"spacedRow"}>
                                    <select name="instruments" onChange={handleChange}
                                            className="form-control login-input"
                                            multiple>
                                        <option value="instrument" defaultValue hidden>Instrument</option>
                                        {instruments.map((item) =>
                                            <option key={item} value={item}>{item}</option>
                                        )}
                                    </select>

                                </Row>
                                <Row className={"spacedRow"}>
                                    <input type="text"
                                           name="bio"
                                           className="form-control login-input"
                                           value={state.bio}
                                           onChange={handleChange}
                                           required
                                           placeholder="Bio"/>
                                </Row>

                                <Row className={"spacedRow"}>
                                    <button className="btn btn-lg login-btn login-btn-backbtn" type="submit"
                                            onClick={changePage}>
                                        Go Back
                                    </button>
                                </Row>
                                <Row>
                                    <button className="btn btn-lg login-btn" type="submit" onClick={handleSubmitClick}>
                                        Submit
                                    </button>
                                </Row>
                            </form>
                        </Col>
                    </Row>
                </Container>
            }

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

export default withRouter(CreateProfile);