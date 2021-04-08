import {Link} from "react-router-dom";
import React from "react";
import logo from '../../../public/images/header_muka_logo.png';
import '../../sass/header.scss';
import { isLogin } from '../auth/auth';

export default function Header(props) {
    return (
        <div className={"headerContainer"}>
            <img src={logo} className={"headerLogo"} alt="Logo"/>
            {!isLogin() && <Link to={'/home'} className={"generalLink"}>Home</Link>}
            {!isLogin() && <Link to={'/about'} className={"generalLink"}>About</Link>}
            {!isLogin() && <Link to={'/sponsors'} className={"generalLink"}>Sponsors</Link>}
            {!isLogin() && <Link to={'/login'} className={"loginLink"}>Login</Link>}
            {!isLogin() && <Link to={'/register'} className={"joinLink"}>Join Us</Link>}
            {isLogin() && <Link to={'/profile'} className={"generalLink"}>Profile</Link>}
            {isLogin() && <Link to={'/dashboard'} className={"generalLink"}>Dashboard</Link>}
        </div>
    );
}
