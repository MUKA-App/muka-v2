import {Link} from "react-router-dom";
import React from "react";

export default function Header(props) {
    return (
        <div>
            {/*<img src={logo} className={"header_logo"} alt="Logo"/>*/}
            <Link to={'/join'}>Join Us</Link>
            <Link to={'/login'}>Login</Link>
            <Link to={'/about'}>About</Link>
            <Link to={'/sponsors'}>Sponsors</Link>
            <Link to={'/home'}>Home</Link>
        </div>
    );
}