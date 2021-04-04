import '../../sass/app.scss';

import React, {useState} from 'react';
import axios from 'axios';
import { withRouter } from "react-router-dom";
import { Container, Row, Col } from 'reactstrap';

function Register(props) {

    const [state, setState] = useState({
        email: "",
        password: "",
        confirmPassword: ""
    })

    const handleChange = (e) => {
        const {id, value} = e.target;
        setState(prevState => ({
            ...prevState,
            [id]: value
        }))
    }

    const sendDetailsToServer = () => {

        const payload = {
            "email": state.email,
            "password": state.password,
        };

        axios.post(process.env.MIX_APP_BASE_URL + "/register", payload)
            .then(function (response) {
                    if (response.status === 201) {
                        console.log("Successful registration.");
                        props.history.push('/login')
                    } else {
                        console.log("Error occurred, code: " + response.status);
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
        } else {
            sendDetailsToServer();
        }
    };

    return (
        <div>
            <form>
                <div>
                    <label>Email address</label>
                    <input type="email"
                           className="form-control"
                           id="email"
                           placeholder="Enter email"
                           value={state.email}
                           onChange={handleChange}
                    />
                </div>
                <div>
                    <label>Password</label>
                    <input type="password"
                           className="form-control"
                           id="password"
                           placeholder="Password"
                           value={state.password}
                           onChange={handleChange}
                    />
                </div>
                <div>
                    <label>Confirm Password</label>
                    <input type="password"
                           className="form-control"
                           id="confirmPassword"
                           placeholder="Confirm Password"
                           value={state.confirmPassword}
                           onChange={handleChange}
                    />
                </div>
                <button
                    type="submit"
                    className="btn btn-primary"
                    onClick={handleSubmitClick}
                >
                    Register
                </button>
            </form>
            <div>
                <span>Already have an account? </span>
                <span onClick={() => props.history.push('/login')}>Login here</span>
            </div>
        </div>
    )
}

export default withRouter(Register);
