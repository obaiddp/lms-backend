<!DOCTYPE html>
<html lang="en" dir="">

    <head>
        {{-- <link rel="stylesheet" href="{{$baseURL}}/assets/zoom-ui/@zoom/videosdk-ui-toolkit/dist/videosdk-ui-toolkit.css">
        --}}
        <link rel="stylesheet"
            href="{{asset('assets/zoom-ui/@zoom/videosdk-ui-toolkit/dist/videosdk-ui-toolkit.css')}}">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style type="text/css">
            .animated-class {
                background-color: black;
                transition: background-color 3s ease;
                animation: fadeIn 3s ease;
            }

            body {
                margin: 0 !important;
                background: #1b1a1d;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            .leaveend {
                display: none !important;
            }

            .navbar-inverse {
                background-color: #313131;
                border-color: #404142;
            }

            .navbar-header h4 {
                margin: 0;
                padding: 15px 15px;
                color: #c4c2c2;
            }

            .navbar-right h5 {
                margin: 0;
                padding: 9px 5px;
                color: #c4c2c2;
            }

            .navbar-inverse .navbar-collapse,
            .navbar-inverse .navbar-form {
                border-color: transparent;
            }

            .header {
                width: 80%;
                margin-top: 0.4rem;
            }

            .header-container {
                width: 96% !important;
                margin: auto;
            }

            .h-100 {
                height: 100% !important;
            }

            .classname {
                color: white;
                margin: 0;
                font-size: 1.2vw;
                line-height: 1.6vw;
            }

            span#end-message-text {
                color: red;
                background: transparent !important;
                font-size: 1.1vw !important;
            }

            .font-size {
                font-size: 1.06rem;
            }
        
            .time-left {
                width: 5rem;
                color: white;
                font-size: 0.8vw !important;
                background: #2b2d2e;
                padding: 4px;
                border-radius: 80px;
                height: 1.6rem;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .time-left span {
                font-size: 1rem !important;
                font-weight: 500 !important;
            }

            .videokit.ng-star-inserted {
                padding: 0 !important;
            }

            .self-view {
                width: 22vw !important;
                max-width: 16vw !important;
                height: 20vh !important;
                margin: 0.4rem 0.2rem !important;
                z-index: 999 !important;
                box-shadow: 0 0 20px #00000052 !important;
                border-radius: 10px !important;
                position: absolute !important;
                right: 0 !important;
                bottom: 1.4rem !important;
            }

            .class-video {
                width: 98%;
                margin: 2px auto;
                position: relative;
            }

            .user-view {
                height: 84vh !important;
                margin-top: 0.4rem !important;
                width: 80vw !important;
            }

            app-videokit.ng-star-inserted {
                position: relative !important;
                display: block;
                width: 100%;
            }

            app-controlskit.ng-star-inserted {
                position: absolute;
                bottom: -11% !important;
                right: 15.8%;
                height: 4.4vw;
            }

            app-controlskit { 
                position: absolute !important; 
                top: 86.4vb !important; 
                left: 56% !important;
            }

            .controlskit[_ngcontent-ng-c2939039469] {
                background-color: transparent !important;
                height: 60px !important;
            }

            .controlskit[_ngcontent-ng-c3592867698]{
                justify-content: flex-end !important;
                padding-right: 8rem !important;
                margin-bottom: 0 !important;
                margin-top: -0.4rem !important;
            }

            html .mat-mdc-fab.mat-primary {
                --mdc-fab-container-color: #5e3fd7 !important;
                border-radius: 100% !important;
                width: 3.2vw;
                height: 3.2vw;
                margin: 0 0.5vw !important;
            }

            button.ctrl-btn.mdc-fab.mat-mdc-fab.mat-primary.mat-mdc-button-base.ng-star-inserted:hover,
            button.ctrl-btn.mdc-fab.mat-mdc-fab.mat-primary.mat-mdc-button-base.ng-star-inserted:active {
                border-radius: 100% !important;
            }

            .mat-badge-medium.mat-badge-overlap .mat-badge-content {
                position: absolute;
                top: 0.5rem !important;
                left: 2.4rem !important;
                background: #ac9bfa !important
            }

            mat-card[_ngcontent-ng-c3963960193] {
                bottom: 0.6rem !important;
                left: 6px !important;
            }

            .mat-mdc-card-content>:last-child:not(.mat-mdc-card-footer) {
                font-size: 1vw !important;
            }

            .mat-mdc-fab:not(.mdc-fab--extended) .mdc-fab__ripple {
                border-radius: 100% !important;
            }

            mat-card[_ngcontent-ng-c3963960193] mat-card-content[_ngcontent-ng-c3963960193] {
                font-size: 1vw !important;
            }

            mat-card[_ngcontent-ng-c3963960193] mat-card-content[_ngcontent-ng-c3963960193] mat-icon[_ngcontent-ng-c3963960193] {
                height: 14px !important;
                width: 14px !important;
                margin-right: 5px !important;
            }

            #exit-buttons-tab {
                right: 8.2%;
                position: absolute;
                top: 88.4vb;
                width: max-content !important;
            }

            #leave_btn {
                box-shadow: -1px 3px 5px -1px rgba(0, 0, 0, .2), 0px 6px 10px 0px rgba(0, 0, 0, .14), 0px 1px 18px 0px rgba(0, 0, 0, .12);
                height: 3vw;
                border: none !important;
                width: 4.4vw;
                padding: 0;
                border-radius: 30px !important;
            }

            .cdk-drag.msger.chat-client {
                display: flex !important;
            }

            #self-view-video[_ngcontent-ng-c3963960193],
            #self-view-canvas[_ngcontent-ng-c3963960193],
            #participant-canvas[_ngcontent-ng-c3963960193] {
                border-radius: 8px;
            }

            #leave_btn svg {
                stroke-width: 0.1px !important;
                width: 2.4vw;
            }

            .phpdebugbar.phpdebugbar-minimized.phpdebugbar-closed {
                display: none;
            }

            .user-name {
                position: absolute;
                top: 88vb;
                left: 8.2%;
                width: max-content;
            }

            .time-div {
                position: absolute;
                top: 92vb;
                left: 8.2%;
                width: max-content;
                padding: 0;
                height: max-content;
            }

            .videokit[_ngcontent-ng-c3963960193] {
                width: 84% !important;
                max-width: 84% !important;
            }

            app-video-wrapper {
                display: flex;
                width: 100%;
                justify-content: center;
            }

            app-chatkit.ng-star-inserted {
                z-index: 9999;
            }

            #gallery-vdieo-container #video-container[_ngcontent-ng-c3222624782]{
                height: 84vh;
                width: 80vw;
            }

            .controls[_ngcontent-ng-c3592867698] {
                overflow: hidden !important;
            }

            @media (max-width: 530px) {
                #self-share-video[_ngcontent-ng-c2500172694] {
                    width: 100vw !important;
                    margin-top: 14vh !important;
                    margin-bottom: 10vh !important;
                }

                .controls[_ngcontent-ng-c3592867698] {
                    justify-content: flex-start;
                    max-width: 120rem !important;
                }

                app-video-wrapper {
                    margin: 0 auto !important;
                    width: 100vw !important;
                }

                .mat-badge-medium.mat-badge-overlap .mat-badge-content {
                    left: 2.1rem !important;
                }

                .time-left span {
                    font-size: 0.8rem !important;
                }

                .time-left {
                    font-size: 0.9rem !important;
                    height: 1.4rem;
                    margin-top: 0.3rem;
                    margin-left: -0.2rem;
                }

                .videokit[_ngcontent-ng-c3963960193] {
                    width: 100% !important;
                    max-width: 100% !important;
                }

                .class-video .col-md-12 {
                    padding: 0 !important;
                }

                .header {
                    margin-top: 0;
                    height: 0;
                }

                .class-video {
                    width: 100%;
                    margin: 0;
                    padding: 0;
                }

                .main-content.container-fluid {
                    padding: 0;
                    height: 100dvh;
                }

                .user-view {
                    margin: 0 !important;
                    height: 89.6dvh !important;
                    width: 100vw !important;
                }

                canvas#participant-canvas {
                    border-radius: 0 !important;
                    width: 100% !important;
                }

                mat-card[_ngcontent-ng-c3963960193] mat-card-content[_ngcontent-ng-c3963960193] {
                    font-size: 0.6rem !important;
                }

                mat-card.mat-mdc-card.mdc-card {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 0.5rem;
                    height: 1.5rem;
                }

                .self-view {
                    width: 6rem !important;
                    max-width: 6rem !important;
                    height: 8.8rem !important;
                    margin: 0.4rem 1rem !important;
                    box-shadow: 0 0 20px #00000042 !important;
                    bottom: 1rem !important;
                }

                .mat-mdc-card-content>:last-child:not(.mat-mdc-card-footer) {
                    display: none !important;
                }

                mat-card[_ngcontent-ng-c3963960193] mat-card-content[_ngcontent-ng-c3963960193] mat-icon[_ngcontent-ng-c3963960193] {
                    font-size: 14px !important;
                    margin-right: 0 !important;
                }

                .user-name {
                    left: 2%;
                    z-index: 999;
                    top: 1% !important;
                    background: #40444a;
                    padding: 0.3rem 0.8rem !important;
                    border-radius: 1rem;
                    right: 6%;
                    width: 96.6vw;
                    height: 1.8rem;
                }

                .classname {
                    font-size: 0.8rem;
                    line-height: 1.2rem;
                    width: 100%;
                    overflow: hidden;
                    white-space: nowrap;
                    text-overflow: ellipsis;
                }

                .time-div {
                    z-index: 999;
                    left: 4%;
                    top: 6.8%;
                }

                span#end-message-text {
                    font-size: 0.8rem !important;
                    font-weight: 600 !important;
                }

                html .mat-mdc-fab.mat-primary {
                    width: 2.2rem;
                    height: 2.2rem;
                    margin: 0 0.2rem !important;
                }

                .controlskit[_ngcontent-ng-c3592867698]{
                    margin-top: 0 !important;
                    height: 3rem !important;
                }

                .mat-mdc-fab-base .mdc-button__label, .mat-mdc-fab-base .mat-icon {
                    font-size: 1.2rem !important;
                    height: 100% !important;
                    display: flex !important;
                    justify-content: center !important;
                    align-items: center !important;
                }

                span#mat-badge-content-0 {
                    width: 1.2rem !important;
                    height: 1.2rem !important;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                .controlskit.ng-star-inserted {
                    padding-right: 76px !important;
                }

                app-controlskit.ng-star-inserted {
                    top: 100% !important;
                    left: -6%;
                    height: auto;
                }

                .controlskit[_ngcontent-ng-c2939039469] {
                    margin-bottom: 0 !important;
                }

                #leave_btn {
                    height: 2.2rem;
                    width: 3rem;
                }

                #leave_btn svg {
                    width: 1.6rem;
                }

                #exit-buttons-tab {
                    right: 2vw;
                    top: 92.3vh;
                }

                app-controlskit {
                    top: 91vh !important;
                    position: absolute;
                    left: 3vw !important;
                }
            }

            @media (min-width: 531px) and (max-width: 767px) {
                #self-share-video[_ngcontent-ng-c2500172694] {
                    width: 100vw !important;
                    margin-top: 14vh !important;
                    margin-bottom: 10vh !important;
                }

                .controls[_ngcontent-ng-c3592867698] {
                    justify-content: flex-start;
                    max-width: 120rem !important;
                }

                app-video-wrapper {
                    margin: 0 auto !important;
                    width: 100vw !important;
                }

                .mat-badge-medium.mat-badge-overlap .mat-badge-content {
                    left: 2.1rem !important;
                }

                .time-left span {
                    font-size: 1rem !important;
                }

                .time-left {
                    font-size: 1rem !important;
                    height: 1.4rem;
                    margin-left: -0.2rem;
                }

                .videokit[_ngcontent-ng-c3963960193] {
                    width: 100% !important;
                    max-width: 100% !important;
                }

                .class-video .col-md-12 {
                    padding: 0 !important;
                }

                .header {
                    margin-top: 0;
                    height: 0;
                }

                .class-video {
                    width: 100%;
                    margin: 0;
                    padding: 0;
                }

                .main-content.container-fluid {
                    padding: 0;
                    height: 100dvh;
                }

                .user-view {
                    margin: 0 !important;
                    height: 90dvh !important;
                    width: 100vw !important;
                }

                canvas#participant-canvas {
                    border-radius: 0 !important;
                    width: 100% !important;
                }

                mat-card[_ngcontent-ng-c3963960193] mat-card-content[_ngcontent-ng-c3963960193] {
                    font-size: 1rem !important;
                }

                mat-card.mat-mdc-card.mdc-card {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 0.8rem;
                    height: 1.8rem;
                }

                .self-view {
                    width: 10rem !important;
                    max-width: 10rem !important;
                    height: 14rem !important;
                    margin: 0.4rem 1rem !important;
                    box-shadow: 0 0 20px #00000042 !important;
                    bottom: 1rem !important;
                }

                .mat-mdc-card-content>:last-child:not(.mat-mdc-card-footer) {
                    display: none !important;
                }

                mat-card[_ngcontent-ng-c3963960193] mat-card-content[_ngcontent-ng-c3963960193] mat-icon[_ngcontent-ng-c3963960193] {
                    font-size: 14px !important;
                    margin-right: 0 !important;
                }

                .user-name {
                    left: 2%;
                    z-index: 999;
                    top: 1% !important;
                    background: #40444a;
                    padding: 0.3rem 0.8rem !important;
                    border-radius: 1rem;
                    right: 6%;
                    width: 96.6vw;
                    height: 1.8rem;
                }

                .classname {
                    font-size: 1rem;
                    line-height: 1.2rem;
                    width: 100%;
                    overflow: hidden;
                    white-space: nowrap;
                    text-overflow: ellipsis;
                }

                .time-div {
                    z-index: 999;
                    left: 3%;
                    top: 5%;
                }

                span#end-message-text {
                    font-size: 1rem !important;
                    font-weight: 600 !important;
                }

                html .mat-mdc-fab.mat-primary {
                    width: 2.6rem;
                    height: 2.6rem;
                    margin: 0 0.6rem !important;
                }

                .controlskit[_ngcontent-ng-c3592867698]{
                    margin-top: 0 !important;
                    height: 3rem !important;
                }

                .mat-mdc-fab-base .mdc-button__label, .mat-mdc-fab-base .mat-icon {
                    font-size: 1.4rem !important;
                    height: 100% !important;
                    display: flex !important;
                    justify-content: center !important;
                    align-items: center !important;
                }

                span#mat-badge-content-0 {
                    width: 1.2rem !important;
                    height: 1.2rem !important;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                .controlskit.ng-star-inserted {
                    padding-right: 76px !important;
                }


                app-controlskit.ng-star-inserted {
                    bottom: -9.2% !important;
                    left: -4%;
                    height: auto;
                }

                .controlskit[_ngcontent-ng-c2939039469] {
                    margin-bottom: 0 !important;
                }

                #leave_btn {
                    height: 2.4rem;
                    width: 3.4rem;
                }

                #leave_btn svg {
                    width: 2rem;
                }

                #exit-buttons-tab {
                    right: 4vw; 
                    top: 91.8vh;
                }

                app-controlskit {
                    top: 91.2vh !important;
                    position: absolute;
                    left: 3vw !important;
                }
            }

            @media (min-width: 768px) and (max-width: 992px) {
                #self-share-video[_ngcontent-ng-c2500172694] {
                    width: 100vw !important;
                    margin-top: 14vh !important;
                    margin-bottom: 10vh !important;
                }

                .controls[_ngcontent-ng-c3592867698] {
                    justify-content: flex-start;
                    max-width: 120rem !important;
                }

                app-video-wrapper {
                    margin: 0 auto !important;
                    width: 100vw !important;
                }

                .mat-badge-medium.mat-badge-overlap .mat-badge-content {
                    left: 3rem !important;
                }

                .time-left span {
                    font-size: 1.2rem !important;
                }

                .time-left {
                    font-size: 1.2rem !important;
                    height: 1.4rem;
                    margin-left: -0.2rem;
                }

                .videokit[_ngcontent-ng-c3963960193] {
                    width: 100% !important;
                    max-width: 100% !important;
                }

                .class-video .col-md-12 {
                    padding: 0 !important;
                }

                .header {
                    margin-top: 0;
                    height: 0;
                }

                .class-video {
                    width: 100%;
                    margin: 0;
                    padding: 0;
                }

                .main-content.container-fluid {
                    padding: 0;
                    height: 100dvh;
                }

                .user-view {
                    margin: 0 !important;
                    height: 90dvh !important;
                    width: 100vw !important;
                }

                canvas#participant-canvas {
                    border-radius: 0 !important;
                    width: 100% !important;
                }

                mat-card[_ngcontent-ng-c3963960193] mat-card-content[_ngcontent-ng-c3963960193] {
                    font-size: 1rem !important;
                }

                mat-card.mat-mdc-card.mdc-card {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 0.8rem;
                    height: 1.8rem;
                }

                .self-view {
                    width: 10rem !important;
                    max-width: 10rem !important;
                    height: 14rem !important;
                    margin: 0.4rem 1rem !important;
                    box-shadow: 0 0 20px #00000042 !important;
                    bottom: 1rem !important;
                }

                .mat-mdc-card-content>:last-child:not(.mat-mdc-card-footer) {
                    display: none !important;
                }

                mat-card[_ngcontent-ng-c3963960193] mat-card-content[_ngcontent-ng-c3963960193] mat-icon[_ngcontent-ng-c3963960193] {
                    font-size: 14px !important;
                    margin-right: 0 !important;
                }

                .user-name {
                    left: 2%;
                    z-index: 999;
                    top: 1% !important;
                    background: #40444a;
                    padding: 0.3rem 0.8rem !important;
                    border-radius: 1rem;
                    right: 6%;
                    width: 96.6vw;
                    height: 2.2rem;
                }

                .classname {
                    font-size: 1.4rem;
                    line-height: 1.4rem;
                    width: 100%;
                    overflow: hidden;
                    white-space: nowrap;
                    text-overflow: ellipsis;
                }

                .time-div {
                    z-index: 999;
                    left: 3%;
                    top: 6%;
                }

                span#end-message-text {
                    font-size: 1.4rem !important;
                    font-weight: 600 !important;
                }

                html .mat-mdc-fab.mat-primary {
                    width: 4rem;
                    height: 4rem;
                    margin: 0 0.6rem !important;
                }

                .controlskit[_ngcontent-ng-c3592867698]{
                    margin-top: 0 !important;
                    height: 6rem !important;
                }

                .mat-mdc-fab-base .mdc-button__label, .mat-mdc-fab-base .mat-icon {
                    font-size: 2rem !important;
                    height: 100% !important;
                    display: flex !important;
                    justify-content: center !important;
                    align-items: center !important;
                }

                span#mat-badge-content-0 {
                    width: 1.5rem !important;
                    height: 1.5rem !important;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                .controlskit.ng-star-inserted {
                    padding-right: 76px !important;
                }

                app-controlskit.ng-star-inserted {
                    bottom: -9.2% !important;
                    left: -1%;
                    height: auto;
                }

                .controlskit[_ngcontent-ng-c2939039469] {
                    margin-bottom: 0 !important;
                }

                #leave_btn {
                    height: 4rem;
                    width: 5rem;
                }

                #leave_btn svg {
                    width: 3rem;
                }

                #exit-buttons-tab {
                    right: 4vw; 
                    top: 91.6vh;
                }

                app-controlskit {
                    top: 90vh !important;
                    position: absolute;
                    left: 3vw !important;
                }
            }

            @media (min-width: 993px) and (max-width: 1199px) {
                #self-share-video[_ngcontent-ng-c2500172694] {
                    width: 100vw !important;
                    margin-top: 14vh !important;
                    margin-bottom: 10vh !important;
                }

                .controls[_ngcontent-ng-c3592867698] {
                    justify-content: flex-start;
                    max-width: 120rem !important;
                }

                app-video-wrapper {
                    margin: 0 auto !important;
                    width: 100vw !important;
                }

                .mat-badge-medium.mat-badge-overlap .mat-badge-content {
                    left: 3rem !important;
                }

                .time-left span {
                    font-size: 1.4rem !important;
                }

                .time-left {
                    font-size: 1.4rem !important;
                    height: 2.2rem;
                    margin-left: -0.8rem;
                    width: 7rem;
                }

                .videokit[_ngcontent-ng-c3963960193] {
                    width: 100% !important;
                    max-width: 100% !important;
                }

                .class-video .col-md-12 {
                    padding: 0 !important;
                }

                .header {
                    margin-top: 0;
                    height: 0;
                }

                .class-video {
                    width: 100%;
                    margin: 0;
                    padding: 0;
                }

                .main-content.container-fluid {
                    padding: 0;
                    height: 100dvh;
                }

                .user-view {
                    margin: 0 !important;
                    height: 90dvh !important;
                    width: 100vw !important;
                }

                canvas#participant-canvas {
                    border-radius: 0 !important;
                    width: 100% !important;
                }

                mat-card[_ngcontent-ng-c3963960193] mat-card-content[_ngcontent-ng-c3963960193] {
                    font-size: 1.4rem !important;
                }

                mat-card.mat-mdc-card.mdc-card {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 0.8rem;
                    height: 1.8rem;
                }

                .self-view {
                    width: 16rem !important;
                    max-width: 16rem !important;
                    height: 22rem !important;
                    margin: 0.4rem 1rem !important;
                    box-shadow: 0 0 20px #00000042 !important;
                    bottom: 1rem !important;
                }

                .mat-mdc-card-content>:last-child:not(.mat-mdc-card-footer) {
                    display: none !important;
                }

                mat-card[_ngcontent-ng-c3963960193] mat-card-content[_ngcontent-ng-c3963960193] mat-icon[_ngcontent-ng-c3963960193] {
                    font-size: 14px !important;
                    margin-right: 0 !important;
                }

                .user-name {
                    left: 2%;
                    z-index: 999;
                    top: 1% !important;
                    background: #40444a;
                    padding: 0.3rem 0.8rem !important;
                    border-radius: 1rem;
                    right: 6%;
                    width: 96.6vw;
                    height: 3.2rem;
                }

                .classname {
                    font-size: 1.6rem;
                    line-height: 2.2rem;
                    width: 100%;
                    overflow: hidden;
                    white-space: nowrap;
                    text-overflow: ellipsis;
                }

                .time-div {
                    z-index: 999;
                    left: 3%;
                    top: 6%;
                }

                span#end-message-text {
                    font-size: 1.6rem !important;
                    font-weight: 600 !important;
                }

                html .mat-mdc-fab.mat-primary {
                    width: 5rem;
                    height: 5rem;
                    margin: 0 0.8rem !important;
                }

                .controlskit[_ngcontent-ng-c3592867698]{
                    margin-top: 0 !important;
                    height: 6rem !important;
                }

                .mat-mdc-fab-base .mdc-button__label, .mat-mdc-fab-base .mat-icon {
                    font-size: 2.2rem !important;
                    display: flex !important;
                    justify-content: center !important;
                    align-items: center !important;
                    width: 100%;
                    margin-right: -2.4rem;
                    height: 100%;
                }

                span#mat-badge-content-0 {
                    width: 1.5rem !important;
                    height: 1.5rem !important;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin-left: 1rem;
                }

                .controlskit.ng-star-inserted {
                    padding-right: 76px !important;
                }

                app-controlskit.ng-star-inserted {
                    bottom: -9.2% !important;
                    left: -1%;
                    height: auto;
                }

                .controlskit[_ngcontent-ng-c2939039469] {
                    margin-bottom: 0 !important;
                    height: 8rem !important;
                }

                #leave_btn {
                    height: 5rem;
                    width: 7rem;
                }

                #leave_btn svg {
                    width: 3rem;
                }

                #exit-buttons-tab {
                    right: 4vw; 
                    top: 91.6vh;
                }

                app-controlskit {
                    top: 91vh !important;
                    position: absolute;
                    left: 3vw !important;
                }
            }

        </style>
    </head>


   

    <body>
        <div align="right">
            <a target="_blank" style = "background-color: #5e3fd7 !important" class = "btn btn-primary" href="{{route('resource.index', [$course_uuid]) }}">{{__('Resources')}}</a>
            {{-- <input type="text" name="courseUUID" id="courseUUID" value="{{$course_uuid}}"> --}}
        </div>
        <div class="main-content container-fluid">
            <input type="hidden" name="baseURL" id="baseURL" value="{{$baseURL}}">
          
            <div class="separator-breadcrumb"></div>
            {{-- <div class="header-nav">
                <div
                    class="row container-fluid mx-auto header-container h-100 d-flex align-items-center justify-content-between">
                    <div class="col-md-3 h-100 d-flex align-items-center justify-content-start">
                        <a class="navbar-brand" href="{{ url('/') }}"> <img
                src="{{ getImageFile(get_option('app_logo')) }}" alt="Logo"></a>
        </div>
        <div class="col-md-9 h-100 d-flex align-items-center justify-content-end">
            <div class="col-md-9">
                <?php if($group_id == null){ ?>
                <h6 class="classname">Quran Education Online Class -
                    <?php echo $lesson_id; ?> -
                    <?php echo $user_name; ?> &
                    <?php echo $student_name; ?>
                </h6>
                <?php } else{ ?>
                <h6 class="classname">Quran Education Online Class -
                    <?php echo $lesson_id; ?> -
                    <?php echo $user_name; ?> & [
                    <?php echo $student_names_json; ?>]
                </h6>
                <?php } ?>
            </div>
            <div class="col-md-3 d-flex justify-content-end">
                <div hidden id="timer" class="time-left">
                    <span id="minutes" class="font-size">00</span>&nbsp;:&nbsp;<span id="seconds"
                        class="font-size">00</span>
                </div>
                <div id="loader-container">
                    <div class="spinner-border text-white" role="status">
                    </div>
                </div>
                <div hidden id="end-message">
                    <span id="end-message-text" class="sr-only"><b>Class Time Is Ended...</b></span>
                </div>
            </div>
        </div>
        </div>
        </div> --}}
        <!-- end of row-->
        <div class="row class-video container-fluid mx-auto">
            <div class="user-name col-md-8 p-0">
                    <?php if($group_id == null){ ?>
                <h6 class="classname">Quran Education Online Class -
                    <?php echo $lesson_id; ?> -
                    <?php echo $user_name; ?> &
                    <?php echo $student_name; ?>
                </h6>
                    <?php } else{ ?>
                <h6 class="classname">Quran Education Online Class -
                    <?php echo $lesson_id; ?> -
                    <?php echo $user_name; ?> & [
                    <?php echo $student_names_json; ?>]
                </h6>
                    <?php } ?>
            </div>
            <div class="time-div col-md-4">
                <div hidden id="timer" class="time-left">
                    <span id="minutes" class="font-size">00</span>&nbsp;:&nbsp;<span id="seconds"
                        class="font-size">00</span>
                </div>
                <div id="loader-container">
                    <div class="spinner-border text-white" role="status">
                    </div>
                </div>
                <div hidden id="end-message">
                    <span id="end-message-text" class="sr-only"><b>Class Time Is Ended...</b></span>
                </div>
            </div>
            <div class="col-md-12">
                <!-- <div style="width:100%;height:100vh;" id="jitsi_meet_container"></div> -->
                <!-- <div id='previewContainer'></div> -->
                <div id='sessionContainer'>
                    
                </div>
                {{-- <div id='uitoolkitContainer'>
                    <div id='controlsContainer'>
                    </div>
                    <div id='videoContainer'></div>
                    <div id='chatContainer'></div>
                    <div id='usersContainer'></div>
                    <div id='settingsContainer'></div>
                </div> --}}
                <div hidden id="exit-buttons-tab">
                    {{-- <button _ngcontent-ng-c2939039469="" mat-fab="" color="primary" aria-label="Change view" class="mat-mdc-menu-trigger ctrl-btn mdc-fab mat-mdc-fab mat-primary mat-mdc-button-base ng-star-inserted" mat-ripple-loader-class-name="mat-mdc-button-ripple" aria-haspopup="menu" aria-expanded="false"><span class="mat-mdc-button-persistent-ripple mdc-fab__ripple"></span><mat-icon _ngcontent-ng-c2939039469="" role="img" class="mat-icon notranslate material-icons mat-ligature-font mat-icon-no-color" aria-hidden="true" data-mat-icon-type="font">grid_view</mat-icon><span class="mdc-button__label"></span><span class="mat-mdc-focus-indicator"></span><span class="mat-mdc-button-touch-target"></span><span class="mat-ripple mat-mdc-button-ripple"></span></button> --}}
                    <button class="form-control bg-danger text-white" id="leave_btn">
                        <svg fill="#ffffff" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path fill-rule="evenodd"
                                    d="M14.4267305,12.3239887 C13.8091494,12.1184418 12.9237465,12.0002 11.9967657,12 C11.0703845,11.9998001 10.1850309,12.1179592 9.56735459,12.323626 C9.29781857,12.4133731 9.10254274,12.5124903 8.99818889,12.5994146 L8.99818886,13.4997844 C8.99830883,14.0560874 8.98526108,14.3378275 8.91482312,14.6766528 C8.76529679,15.3959143 8.36503921,15.9530303 7.5979407,15.9971778 C5.57992549,16.3324217 4.23196922,16.5 3.49954722,16.5 C2.04222339,16.5 1,15.1968274 1,14 L1,12.5 C1,8.77610714 6.02664974,5.99871171 11.9971973,6.00000002 C17.9690798,6.00128863 22.993963,8.77688238 22.9935942,12.4728433 C22.9981103,12.6390833 23.0000363,12.8114009 22.9999995,13.0054528 C22.9999727,13.1468201 22.9992073,13.2587316 22.9969405,13.5090552 C22.9947039,13.7560368 22.993963,13.8651358 22.993963,14 C22.993963,15.1895648 21.9503425,16.5 20.4944157,16.5 C19.7626874,16.5 18.4165903,16.332739 16.4017544,15.9981299 C15.3495506,15.9554142 15.0603932,15.1844357 15.0052983,14.044091 C14.9974219,13.8810653 14.9958289,13.7545264 14.9957743,13.5011312 C14.9956956,12.9832104 14.9956891,12.9405386 14.9957547,12.5995238 C14.8913892,12.5126847 14.6961745,12.4136666 14.4267305,12.3239887 Z M6.99818889,13.5 L6.99818889,12.5 C6.99818889,10.7340787 9.20464625,9.99939745 11.9971973,10 C14.7913808,10.0006029 16.9957741,10.7342819 16.9957741,12.5 C16.9956885,12.9366661 16.9956885,12.9366661 16.995774,13.4997844 C16.995822,13.7225055 16.9971357,13.8268559 17.0029681,13.9475751 C17.0051195,13.992103 17.0078746,14.0335402 17.0110607,14.0715206 C18.7614943,14.3571487 19.9381265,14.5 20.4944157,14.5 C20.7329265,14.5 20.993963,14.1722263 20.993963,14 C20.993963,13.8570865 20.9947313,13.7439632 20.9970225,13.4909448 C20.9992358,13.2465315 20.9999742,13.1385601 20.9999995,13.0050735 C21.0000331,12.8280282 20.998305,12.6734088 20.993963,12.5 C20.993963,10.2010869 17.0111151,8.00108196 11.9967657,7.99999998 C6.98400975,7.99891833 3,10.2002196 3,12.5 L3,14 C3,14.1781726 3.2573842,14.5 3.49954722,14.5 C4.05591217,14.5 5.23278898,14.3571098 6.98361703,14.071404 C6.99451507,13.9374564 6.99824508,13.76066 6.99818889,13.5 Z">
                                </path>
                            </g>
                        </svg>
                    </button>
                </div>
            </div>
            <!-- end of col-->
        </div>
        </div>
        </div>
    </body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

</html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsrsasign/10.5.24/jsrsasign-all-min.js"></script>
<script src="{{asset('assets/zoom-ui/@zoom/videosdk-ui-toolkit/index.js')}}" type="module"></script>
<script src="{{asset('frontend/assets/vendor/jquery/jquery-3.6.0.min.js')}}"></script>
<script src="{{asset('frontend/assets/vendor/jquery/popper.min.js')}}"></script>
<script src="{{asset('frontend/assets/vendor/bootstrap/js/bootstrap.min.js')}}"></script>
<script type="text/javascript">
    var base_url = $('#baseURL').val();
    $(document).ready(function () {
});
</script>
<script type="module">
    import uitoolkit from "{{asset('assets/zoom-ui/@zoom/videosdk-ui-toolkit/index.js')}}";
    import previewkit from "{{asset('assets/zoom-ui/@zoom/videosdk-ui-toolkit/index.js')}}";

    var meeting_id = '<?php echo $meeting_id;?>';
    var meeting_password = '<?php echo $meeting_password;?>';
    var meeting_signature = '<?php echo $meeting_signature;?>';
    var session_name = '<?php echo $session_name;?>';
    var group_id = <?php echo $group_id;?>;
    var user_name = '<?php echo $user_name;?>';
    var user_type = '<?php echo $user_type;?>';
    var user_id = '<?php echo $user_id;?>';
    var base_url = '<?php echo $baseURL;?>';
    var meeting_end_url = '<?php echo $baseURL;?>/instructor/live-class/live-class/class-schedule';
    var startTime = <?php echo $start_time;?>;
    var endTime = <?php echo $end_time;?>;

    if(group_id == 0){
        group_id = null;
    }

    let hostId = null;     


    function generateSignature(apiKey, sessionName, sessionKey, role,apiSecret) {
        const iat = Math.floor(Date.now() / 1000)
        const exp = iat + 80 * 80 * 2
        const oHeader = { alg: 'HS256', typ: 'JWT' }
        const oPayload = {
            app_key: apiKey,
            role_type: role,
            tpc: sessionName,
            version : 1,
            iat: iat,
            exp: exp
        };
        const sHeader = JSON.stringify(oHeader);
        const sPayload = JSON.stringify(oPayload);
        const sJWT = KJUR.jws.JWS.sign('HS256', sHeader, sPayload, apiSecret);
        return sJWT;
}

    $(document).ready(function () {

        var meetConfig = {
            apiKey: "<?php echo $sdkKey;?>",
            apiSecret: "<?php echo $sdkSecret;?>",
            meetingNumber: "<?php echo $meeting_id;?>",
            userName: "<?php echo $user_name;?>",
            passWord: "<?php echo $meeting_password;?>",
            leaveUrl: "<?php echo $baseURL;?>/instructor/live-class/live-class/class-schedule",
            role: 1,
        };
        
        var sessionContainer = document.getElementById('sessionContainer')
            // var uitoolkitContainer = document.getElementById('uitoolkitContainer')
            // var controlsContainer = document.getElementById('controlsContainer')
            // var videoContainer = document.getElementById('videoContainer')
            // var chatContainer = document.getElementById('chatContainer')
            // var usersContainer = document.getElementById('usersContainer')
            // var settingsContainer = document.getElementById('settingsContainer')

        var config = {
            videoSDKJWT: '',
            sessionName: session_name,
            userName: user_name,
            features: ['video', 'audio', 'settings', 'users', 'chat', 'share'],
            options: { 
        init: {
            'enforceMultipleVideos' : true,
            // 'enforceVirtualBackground' : true,
        },
        audio: {
            'backgroundNoiseSuppression': true,
            'mute' : true,
            // 'speakerOnly':true
        },
        video: {
            'virtualBackground': true,
            'originalRatio': true,
        },
},
};


    var role = 1;
  

      if(meeting_signature != ''){
        // var signature1 = meeting_signature;
        var signature1 = generateSignature('<?php echo $sdkKey;?>', '<?php echo $session_name;?>',  '<?php echo $meeting_password;?>', 1, '<?php echo $sdkSecret;?>');
        if(group_id == null){
    $.ajax({
    type: "GET",
    url:  "<?php echo $baseURL;?>/instructor/live-class/live-class/UpdateMeetingSignature/<?php echo $meeting_id;?>",
    data: {
        'meetingSignature':signature1,
    },
            success: (function (result) {
            })
        })
    }
    else{
        $.ajax({
    type: "GET",
    url:  "<?php echo $baseURL;?>/instructor/live-class/live-class/UpdateMeetingSignature/<?php echo $meeting_id;?>/<?php echo $group_id;?>",
    data: {
        'meetingSignature':signature1,
    },
            success: (function (result) {
            })
        })  
    }
    }
    else{
        var signature1 = generateSignature('<?php echo $sdkKey;?>', '<?php echo $session_name;?>',  '<?php echo $meeting_password;?>', 1, '<?php echo $sdkSecret;?>');
        if(group_id == null){
    $.ajax({
    type: "GET",
    url:  "<?php echo $baseURL;?>/instructor/live-class/live-class/UpdateMeetingSignature/<?php echo $meeting_id;?>",
    data: {
        'meetingSignature':signature1,
    },
            success: (function (result) {
            })
        })
    }
    else{
        $.ajax({
    type: "GET",
    url:  "<?php echo $baseURL;?>/instructor/live-class/live-class/UpdateMeetingSignature/<?php echo $meeting_id;?>/<?php echo $group_id;?>",
    data: {
        'meetingSignature':signature1,
    },
            success: (function (result) {
            })
        })  
    }

    }
  
    if(signature1){
      
        config.videoSDKJWT = signature1;
        console.log(signature1)
        joinZoomSession();
    }
    console.log(config)
 
    function joinZoomSession() {
        console.log("started joined");
        console.log(config);
    // uitoolkit.joinSession(sessionContainer, config)
    // uitoolkit.joinSession(sessionContainer, config,meeting_id,group_id,endTime,base_url,'Tutor',0)

    uitoolkit.joinSession(sessionContainer, config,meeting_id,group_id,base_url,endTime,meeting_end_url,'Tutor',0)
    // startTimer(meeting_id,group_id);
    uitoolkit.onSessionJoined(() => {
      document.querySelector('[aria-label="Change view"]')?.click();
      [...document.querySelectorAll("span")].filter((span) => span.textContent?.includes("Gallery"))[0].click();
    })
    uitoolkit.onSessionClosed(sessionClosed)
}


var sessionClosed = (() => {
  
    console.log('session closed')
    uitoolkit.closeSession(sessionContainer)
    document.getElementById('join-flow').style.display = 'block'
})


var sessionJoined = (() => {   
    console.log('session joined')
  

})



        });


        $('#leave_btn').on('click', function () {
            uitoolkit.closeSession(sessionContainer)
            window.location.href = meeting_end_url;
 
        });


        $('#end_btn').on('click', function () {
            var result = confirm("Are you sure you want to end this class?");
            // If user confirms, proceed with action
            if (result) {
                $('#timer').attr('hidden',true);
                $('#timer').attr('hidden','hidden');
        $('#end-message-text').text('Class Ended By Tutor.');
        $('#end-message-text').css('font-weight','bold');
        $('#end-message-text').css('font-size','20px');
        $('#end-message-text').css('color','red');
        $('#end-message-text').addClass('animated-class');
        $('#end-message').removeAttr('hidden').show();
                setTimeout(() => {
                    if(group_id == null){
    $.ajax({
    type: "GET",
    url:  "<?php echo $baseURL;?>/instructor/live-class/live-class/UpdateStatusZoom/<?php echo $meeting_id;?>",
            success: (function (result) {
                uitoolkit.closeSession(sessionContainer)
                uitoolkit.is_end();
                window.location.href = meeting_end_url;
            })
        })
    }
    else{
        $.ajax({
    type: "GET",
    url:  "<?php echo $baseURL;?>/instructor/live-class/live-class/UpdateStatusGroupZoom/<?php echo $meeting_id;?>/<?php echo $group_id;?>",
            success: (function (result) {
                uitoolkit.closeSession(sessionContainer)
                uitoolkit.is_end();
                window.location.href = meeting_end_url;
            })
        })  
    }
                }, 2000);

  
            } else {
                // Optionally, do something if the user cancels
            }
                       
        });



    document.addEventListener('DOMContentLoaded', () => {
    const hiddenDiv = document.querySelector('div.ng-star-inserted[_ngcontent-ng-c1600015478]');

    if (hiddenDiv && hiddenDiv.hasAttribute('hidden')) {
        console.log("Specific div has hidden attribute:", hiddenDiv);
        resetStyles();
    } else {
        console.log("Specific div does not have hidden attribute.");
        applyStylesBasedOnWindowSize();
    }
});

const observer = new MutationObserver(() => {
    const hiddenDiv = document.querySelector('div.ng-star-inserted[_ngcontent-ng-c1600015478]');
    const videoKitElement = document.querySelector('app-videokit');
    const galleryVideoContainer = document.querySelector('#gallery-vdieo-container');

    if (hiddenDiv && hiddenDiv.hasAttribute('hidden')) {
        console.log("Specific div has hidden attribute after DOM change:", hiddenDiv);
        // Reset CSS styles here
        resetStyles();
    } else {
        console.log("Specific div does not have hidden attribute after DOM change.");
        applyStylesBasedOnWindowSize();
    }

    if (galleryVideoContainer && window.getComputedStyle(galleryVideoContainer).display === 'flex') {
        if (videoKitElement) {
            videoKitElement.style.display = 'none';
            console.log("Hiding app-videokit since gallery-vdieo-container is flex.");
        }
    } else {
        if (videoKitElement) {
            videoKitElement.style.display = '';
            console.log("Showing app-videokit since gallery-vdieo-container is not flex.");
        }
    }
});

observer.observe(document.body, { childList: true, subtree: true, attributes: true });

function applyStylesBasedOnWindowSize() {
    const SmallMobile = window.matchMedia('(min-width: 300px) and (max-width: 530px)');
    const LargeMobile = window.matchMedia('(min-width: 531px) and (max-width: 767px)');
    const SmallTablet = window.matchMedia('(min-width: 768px) and (max-width: 992px)');
    const LargeTablet = window.matchMedia('(min-width: 993px) and (max-width: 1199px)');
    const Desktop = window.matchMedia('(min-width: 1200px)');

    if (SmallMobile.matches) {
        applyConditionalStylesSmallMobile();
    } else if (LargeMobile.matches) {
        applyConditionalStylesLargeMobile();
    }  else if (SmallTablet.matches) {
        applyConditionalStylesSmallTablet();
    }  else if (LargeTablet.matches) {
        applyConditionalStylesLargeTablet();
    } else if (Desktop.matches) {
        applyConditionalStylesDesktop();
    }
}

function applyConditionalStylesSmallMobile() {
    // Helper function to apply styles with !important
    function applyStylesWithImportant(element, styles) {
        for (const [property, value] of Object.entries(styles)) {
            element.style.setProperty(property, value, 'important');
        }
    }

    const elementsToStyle = [
        { selector: 'app-video-wrapper', styles: { display: 'flex','flex-direction': 'column-reverse' } },
        { selector: '.user-view[_ngcontent-ng-c3099019308]', styles: { height: '24vh', width: '46vw', margin: '0.6rem' } },
        { selector: '#self-share-video', styles: { height: '40vh', width:'98vw', 'margin-top':'5rem', 'margin-bottom':'3rem', 'margin-left':'auto','margin-right':'auto'} },
        { selector: '.self-view', styles: {width: '46vw','max-width':' 46vw', height: '24vh', margin: '0.4rem 0.6rem',bottom: '0.26rem'} },
        { selector: '.videokit.ng-star-inserted', styles: {display: 'flex','flex-direction': 'row'} },
        { selector: '#participant-canvas', styles: {'border-radius': '1rem'} },
    ];

    elementsToStyle.forEach(({ selector, styles }) => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(element => {
            applyStylesWithImportant(element, styles);
        });
    });
}

function applyConditionalStylesLargeMobile() {
    function applyStylesWithImportant(element, styles) {
        for (const [property, value] of Object.entries(styles)) {
            element.style.setProperty(property, value, 'important');
        }
    }

    const elementsToStyle = [
        { selector: 'app-video-wrapper', styles: { display: 'flex','flex-direction': 'column-reverse' } },
        { selector: '.user-view[_ngcontent-ng-c3099019308]', styles: { height: '24vh', width: '46vw', margin: '0.6rem' } },
        { selector: '#self-share-video', styles: { height: '40vh', width:'98vw','margin-top':'8rem', 'margin-bottom':'6rem', 'margin-left':'auto','margin-right':'auto'} },
        { selector: '.self-view', styles: {width: '46vw','max-width':' 46vw', height: '24vh', margin: '0.4rem 0.6rem',bottom: '0.26rem'} },
        { selector: '.videokit.ng-star-inserted', styles: {display: 'flex','flex-direction': 'row', margin: '0'} },
        { selector: '#participant-canvas', styles: {'border-radius': '1rem'} },
    ];

    elementsToStyle.forEach(({ selector, styles }) => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(element => {
            applyStylesWithImportant(element, styles);
        });
    });
}

function applyConditionalStylesSmallTablet() {
    function applyStylesWithImportant(element, styles) {
        for (const [property, value] of Object.entries(styles)) {
            element.style.setProperty(property, value, 'important');
        }
    }

    const elementsToStyle = [
        { selector: 'app-video-wrapper', styles: { display: 'flex','flex-direction': 'column-reverse' } },
        { selector: '.user-view[_ngcontent-ng-c3099019308]', styles: { height: '24vh', width: '46vw', margin: '0.6rem' } },
        { selector: '#self-share-video', styles: { height: '40vh', width:'98vw', 'margin-top':'8rem', 'margin-bottom':'6rem', 'margin-left':'auto','margin-right':'auto'} },
        { selector: '.self-view', styles: {width: '46vw','max-width':' 46vw', height: '24vh', margin: '0.4rem 0.6rem',bottom: '0.26rem'} },
        { selector: '.videokit.ng-star-inserted', styles: {display: 'flex','flex-direction': 'row', margin: '0'} },
        { selector: '#participant-canvas', styles: {'border-radius': '1rem'} },
    ];

    elementsToStyle.forEach(({ selector, styles }) => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(element => {
            applyStylesWithImportant(element, styles);
        });
    });
}

function applyConditionalStylesLargeTablet() {
    function applyStylesWithImportant(element, styles) {
        for (const [property, value] of Object.entries(styles)) {
            element.style.setProperty(property, value, 'important');
        }
    }

    const elementsToStyle = [
        { selector: 'app-video-wrapper', styles: { display: 'flex','flex-direction': 'column-reverse' } },
        { selector: '.user-view[_ngcontent-ng-c3099019308]', styles: { height: '24vh', width: '46vw', margin: '0.6rem' } },
        { selector: '#self-share-video', styles: { height: '40vh', width:'98vw','margin-top':'8rem', 'margin-bottom':'6rem', 'margin-left':'auto','margin-right':'auto'} },
        { selector: '.self-view', styles: {width: '46vw','max-width':' 46vw', height: '24vh', margin: '0.4rem 0.6rem',bottom: '0.26rem'} },
        { selector: '.videokit.ng-star-inserted', styles: {display: 'flex','flex-direction': 'row', margin: '0'} },
        { selector: '#participant-canvas', styles: {'border-radius': '1rem', width: '46vw'} },
    ];

    elementsToStyle.forEach(({ selector, styles }) => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(element => {
            applyStylesWithImportant(element, styles);
        });
    });
}

function applyConditionalStylesDesktop() {
    function applyStylesWithImportant(element, styles) {
        for (const [property, value] of Object.entries(styles)) {
            element.style.setProperty(property, value, 'important');
        }
    }

    const elementsToStyle = [
        { selector: 'app-videokit.ng-star-inserted', styles: { width: '30%', 'margin-left': 'auto' } },
        { selector: '.class-video', styles: { width: '100%', padding: '0', margin: '0' } },
        { selector: 'app-sharekit', styles: { width: '70%' } },
        { selector: 'app-sharekit.ng-star-inserted', styles: { width: '70%' } },
        { selector: 'app-video-wrapper', styles: { display: 'flex', 'flex-direction': 'row-reverse' } },
        { selector: '.videokit[_ngcontent-ng-c3099019308]', styles: { width: '94%', 'margin-left': '1rem' } },
        { selector: '.user-view', styles: { height: '16.4vw', 'margin-top': '2.6vw', 'margin-bottom': '0', width: '90%', 'margin-left': 'auto' } },
        { selector: '#self-share-video', styles: { height: '36vw', 'margin-top': '1.8vw', background: 'white', 'border-radius': '1.2vw' } },
        { selector: '.self-view', styles: { height: '16vw', width: '100%', margin: '0.6rem 0.16rem','max-width': '26%', position: 'absolute', right: '1.2rem', top: '52%' } },
        { selector: '#participant-canvas', styles: {'border-radius': '1rem', width: '100%'} }
    ];

    elementsToStyle.forEach(({ selector, styles }) => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(element => {
            applyStylesWithImportant(element, styles);
        });
    });
}

function resetStyles() {
    // Helper function to reset styles to initial values
    function resetElementStyles(element) {
        const initialStyles = {
            'width': '',
            'margin-left': '',
            'padding': '',
            'margin': '',
            'display': '',
            'flex-direction': '',
            'margin-right': '',
            'height': '',
            'background': '',
            'border-radius': '',
            'position': '',
            'right': '',
            'top': ''
        };

        for (const [property, value] of Object.entries(initialStyles)) {
            element.style.setProperty(property, value);
        }
    }

    const elementsToReset = [
        'app-videokit.ng-star-inserted',
        '.class-video',
        'app-sharekit',
        'app-sharekit.ng-star-inserted',
        'app-video-wrapper',
        '.videokit[_ngcontent-ng-c3963960193]',
        '.videokit[_ngcontent-ng-c3099019308]',
        '.user-view',
        '#self-share-video',
        '.self-view'
    ];

    elementsToReset.forEach(selector => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(element => {
            resetElementStyles(element);
        });
    });
}

// Listen for changes in the viewport size
window.addEventListener('resize', () => {
    resetStyles();
    const hiddenDiv = document.querySelector('div.ng-star-inserted[_ngcontent-ng-c1600015478]');
    
    if (hiddenDiv && hiddenDiv.hasAttribute('hidden')) {
        console.log("Specific div has hidden attribute after DOM change:", hiddenDiv);
        // Reset CSS styles here
        resetStyles();
    } else {
        console.log("Specific div does not have hidden attribute after DOM change.");
        applyStylesBasedOnWindowSize();
    }
});


</script>