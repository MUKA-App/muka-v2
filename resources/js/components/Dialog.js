import React from 'react';

export default function Dialog (props) {
    return (
        <div>
            <h1>{ props.head }</h1>
            <h3>{ props.body }</h3>
        </div>
    );
}