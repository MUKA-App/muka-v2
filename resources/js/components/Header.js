import {Link} from "react-router-dom";
import React from "react";
import logo from '../../../public/images/header_muka_logo.png';
import '../../sass/header.scss';

export default function Header(props) {
    return (
        <div className={"headerContainer"}>
            <img src={logo} className={"headerLogo"} alt="Logo"/>
            <Link to={'/home'} className={"generalLink"}>Home</Link>
            <Link to={'/about'} className={"generalLink"}>About</Link>
            <Link to={'/sponsors'} className={"generalLink"}>Sponsors</Link>
            <Link to={'/login'} className={"loginLink"}>Login</Link>
            <Link to={'/join'} className={"joinLink"}>Join Us</Link>
        </div>
    );
}
