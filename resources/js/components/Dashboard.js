import React, {useEffect} from 'react';
import {withRouter} from "react-router-dom";
import axios from "axios";

function Dashboard(props) {

    // redirect to create profile if user does not have profile
    useEffect(() => {
        axios.get(process.env.MIX_APP_BASE_URL + "/api/profile")
            .catch(error => {
                props.history.push('/profiles/create');
            });
    }, []);

    return (
        <div>Dashboard</div>
    )
}

export default withRouter(Dashboard);