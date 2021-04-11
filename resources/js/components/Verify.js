import React from 'react';
import {useParams} from 'react-router-dom';
import axios from "axios";
import {withRouter} from "react-router-dom";

function Verify(props) {

    const {token} = useParams();

    axios.post(process.env.MIX_APP_BASE_URL + "/register/verify", { token })
        .then(function (response) {
                if (response.status === 200) {
                    console.log("Successful verification");
                    localStorage.setItem('auth', 'true');
                    props.history.push(response.data.redirect);
                } else {
                    console.log("Error occured with code: " + response.status);
                }
            }
        ).catch(function (error) {
        console.log(error);
    });
}

export default withRouter(Verify);