import '../../sass/app.scss';

import React, {useState} from 'react';
import axios from 'axios';
import { withRouter } from "react-router-dom";


function Register(props) {

    const [state, setState] = useState({
        email: "",
        password: "",
        confirmPassword: "",
        successMessage: null
    })

    const handleChange = (e) => {
        const {id, value} = e.target
        setState(prevState => ({
            ...prevState,
            [id]: value
        }))
    }

    const sendDetailsToServer = () => {
        if (state.email.length && state.password.length) {
            // props.showError(null);
            const payload = {
                "email": state.email,
                "password": state.password,
            }
            // TODO: this is hard coded. How to get .env of laravel?
            axios.post('https://muka.local/register/', payload)
                .then(function (response) {
                    if (response.status === 200) {
                        setState(prevState => ({
                            ...prevState,
                            'successMessage': 'Registration successful. Redirecting to home page..'
                        }))
                        redirectToHome();
                        // props.showError(null)
                    } else {
                        console.log("Error occurred.");
                        // props.showError("Some error ocurred");
                    }
                }
                )
                .catch(function (error) {
                    console.log(error);
                });
        } else {
            // props.showError('Please enter valid username and password')
        }

    }
    const redirectToHome = () => {
        // props.updateTitle('Home')
        props.history.push('/home');
    }
    const redirectToLogin = () => {
        // props.updateTitle('Login')
        props.history.push('/login');
    }
    const handleSubmitClick = (e) => {
        e.preventDefault();
        if (state.password === state.confirmPassword) {
            sendDetailsToServer()
        } else {
            console.log("Non matching passwords");
            // props.showError('Passwords do not match');
        }
    }

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
            <div role="alert">
                {state.successMessage}
            </div>
            <div>
                <span>Already have an account? </span>
                <span onClick={() => redirectToLogin()}>Login here</span>
            </div>
        </div>
    )
}

export default withRouter(Register);
