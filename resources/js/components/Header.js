import {Link} from "react-router-dom";
import React, {useEffect, useState} from "react";
import logo from '../../../public/images/header_muka_logo.png';
import '../../sass/header.scss';
import { isLogin} from '../auth/auth';
import {withRouter} from "react-router-dom";

function Header (props) {

    const [state, setState] = useState(false)

    useEffect(() => setState(isLogin()), [props])

    const handleLogout = () => {
        localStorage.removeItem('auth');
        props.history.push('/home');
        //TODO: send backend request
        setState(false)
    }

    return (
        <div className={"headerContainer"}>
            <img src={logo} className={"headerLogo"} alt="Logo"/>
            {!state && <Link to={'/home'} className={"generalLink"}>Home</Link>}
            {!state && <Link to={'/about'} className={"generalLink"}>About</Link>}
            {!state && <Link to={'/sponsors'} className={"generalLink"}>Sponsors</Link>}
            {!state && <Link to={'/login'} className={"loginLink"}>Login</Link>}
            {!state && <Link to={'/register'} className={"joinLink"}>Join Us</Link>}
            {state && <Link to={'/dashboard'} className={"generalLink"}>Dashboard</Link>}
            {state && <Link to={'/profile'} className={"generalLink"}>Profile</Link>}
            {state && <Link to={'/logout'} className={"generalLink"} onClick={() => handleLogout()}>Logout</Link>}
        </div>
    );
}
export default withRouter(Header);