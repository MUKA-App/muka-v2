import React from 'react';
import {useParams} from 'react-router-dom';
import axios from "axios";

export default function Verify() {

    const {token} = useParams();

    axios.post(process.env.MIX_APP_BASE_URL + "/register/verify", token)
        .then(function (response) {
                if (response.status === 200) {
                    console.log("Successful verification");
                    //    TODO: get path from response
                } else {
                    console.log("Error occured with code: " + response.status);
                }
            }
        ).catch(function (error) {
        console.log(error);
    });


    return (
        <div>Login successful. {token}</div>
    );
}