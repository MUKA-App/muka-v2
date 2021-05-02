import React from 'react';
import {useParams} from 'react-router-dom';
import axios from "axios";
import {withRouter} from "react-router-dom";
import {login} from "../auth/auth";

function Verify(props) {

    const {token} = useParams();

    axios.post(process.env.MIX_APP_BASE_URL + "/register/verify", {token})
        .then(function (response) {
                if (response.status === 200) {
                    login();
                    props.history.push('/profiles/create');
                } else {
                    console.log(response);
                }
            }
        ).catch(error => console.log(error));

    return (<div>Verify</div>);
}

export default withRouter(Verify);
