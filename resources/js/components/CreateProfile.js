import '../../sass/login.scss';
import React, {useState} from 'react';
import {Col, Container, Row} from "reactstrap";
import {toast, ToastContainer} from "react-toastify";
import axios from "axios";
import {withRouter} from "react-router-dom";

function CreateProfile(props) {

    axios.get(process.env.MIX_APP_BASE_URL + "/api/profile")
        .then(response =>{
            if(response.status === 200){
                console.log("GOTODASH");
                props.history.push('/dashboard');
            }
        })
        .catch(error => console.log(error));

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
        } else {
            value = e.target.value;
        }
        setState(prevState => ({
            ...prevState,
            [id]: value
        }))
    };

    const handleSubmitClick = (e) => {
        e.preventDefault();
        const payload = {
            "first_name": state.firstName,
            "last_name": state.lastName,
            "bio": state.bio,
            "gender": state.gender,
            "country": state.country,
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
                        notify(error.response.data.errors);
                    }
                })
                .catch(error => {
                    Object.values(error.response.data.errors).forEach(e => notify(e.join(',')));
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

    const instruments = ["Accordion", "Acoustic Guitar", "Bagpipes", "Banjo", "Bass Guitar", "Bongo Drums", "Bugle", "Cello", "Clarinet", "Cymbals", "Drums", "Electric Guitar",
        "Flute", "French Horn", "Harmonica", "Harp", "Keyboard", "Maracas", "Organ", "Pan Flute", "Piano", "Recorder", "Saxophone", "Sitar", "Tambourine",
        "Percussion", "Trombone", "Trumpet", "Tuba", "Ukulele", "Violin", "Xylophone", "Bassoon", "Castanets", "Didgeridoo", "Double Bass", "Gong", "Harpsichord",
        "Lute", "Mandolin", "Oboe", "Piccolo", "Viola", "Singer", "Composer/Songwriter", "Euphonium", "DJ", "Producer", "Fiddle", "Vocals"
    ];

    return (
        <div>
            {state.pageNo === 1 ?
                <Container>
                    <Row>
                        <Col
                            className={"halfColumn col-sm pt-lg-6 pt-md-3 pt-sm-1 pt-0 pr-lg-5 pr-xl-6 justify-content-center align-items-center"}>
                            <div className={" vertical-center"}>
                                <h1 className={"welcomeText"}>Get Ready to Connect</h1>
                                {/*put here upload photo input field*/}
                                <h5 className={"joinText"}>Join MUKA now and find people with the same passion as
                                    you!</h5>
                            </div>
                        </Col>
                        <Col
                            className={"halfColumn col-sm pt-lg-6 pt-md-3 pt-sm-1 pt-0 pr-lg-5 pr-xl-6 justify-content-center align-items-center"}>
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
                                           name="country"
                                           className="form-control"
                                           value={state.country}
                                           onChange={handleChange}
                                           required
                                           placeholder="Country"/>
                                </Row>
                                <Row className={"spacedRow"}>
                                    <input type="text"
                                           name="city"
                                           className="form-control"
                                           value={state.city}
                                           onChange={handleChange}
                                           required
                                           placeholder="City"/>
                                </Row>
                                <Row className={"spacedRow"}>
                                    <input type="date"
                                           name="birth"
                                           className="form-control"
                                           value={state.birth}
                                           onChange={handleChange}
                                           required
                                           placeholder="Birthday"/>
                                </Row>
                                <Row className={"spacedRow"}>
                                    <select name="gender" onChange={handleChange} className="form-control">
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
                            <div className={" vertical-center"}>
                                <h1 className={"welcomeText"}>Get Ready to Connect</h1>
                                {/*put here upload photo input field*/}
                                <h5 className={"joinText"}>Join MUKA now and find people with the same passion as
                                    you!</h5>
                            </div>
                        </Col>
                        <Col
                            className={"halfColumn col-sm pt-lg-6 pt-md-3 pt-sm-1 pt-0 pr-lg-5 pr-xl-6 justify-content-center align-items-center"}>
                            <form>
                                <Row className={"spacedRow"}>
                                    <input type="text"
                                           name="institution"
                                           className="form-control"
                                           value={state.institution}
                                           onChange={handleChange}
                                           required
                                           placeholder="Institution"/>
                                </Row>
                                <Row className={"spacedRow"}>
                                    <select name="instruments" onChange={handleChange} className="form-control"
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
                                           className="form-control"
                                           value={state.bio}
                                           onChange={handleChange}
                                           required
                                           placeholder="Bio"/>
                                </Row>

                                <Row className={"spacedRow"}>
                                    <button className="" type="submit" onClick={changePage}>
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