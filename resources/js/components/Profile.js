import React, {useEffect} from 'react';
import {hasProfile} from "../auth/auth";
import {withRouter} from "react-router-dom";

function Profile(props) {

    // redirect to create profile if user does not have profile
    useEffect(() => {
        if (!hasProfile().valueOf()) {
            props.history.push("/profiles/create");
        }
    }, []);

    return (
        <div>Profile</div>
    )
}

export default withRouter(Profile);