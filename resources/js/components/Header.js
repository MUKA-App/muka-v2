import {Link} from "react-router-dom";
import React from "react";
import '../../css/header.css';

export default function Header(props) {
    return (
        <div className={"headerContainer"}>
            {/*<img src={logo} className={"header_logo"} alt="Logo"/>*/}
            <Link to={'/join'} className={"joinLink"}>Join Us</Link>
            <Link to={'/login'} className={"loginLink"}>Login</Link>
            <Link to={'/about'} className={"generalLink"}>About</Link>
            <Link to={'/sponsors'} className={"generalLink"}>Sponsors</Link>
            <Link to={'/home'} className={"generalLink"}>Home</Link>
        </div>
    );
}