import React from 'react';

export default function Dialog (props) {
    return (
        <div>
            <p>{ props.head }</p>
            <p>{ props.body }</p>
        </div>
    );
}