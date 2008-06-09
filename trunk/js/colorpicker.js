
(function colorPickerNamespace(){
    var cp = null;

    var imgBase = 'images/colorpicker/';

    function hex(c){
        c=parseInt(c).toString(16);
        return c.length<2?"0"+c:c
    }

    function mouseCoordinates(ev){
        ev = ev || window.event;
        if(ev.pageX || ev.pageY)
            return {x:ev.pageX, y:ev.pageY};
        return {x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,
                  y:ev.clientY + document.body.scrollTop  - document.body.clientTop};
    }

    function getPosition(obj){
        var left = 0;
        var top  = 0;

        while (obj.offsetParent){
            left += obj.offsetLeft;
            top  += obj.offsetTop;
            obj   = obj.offsetParent;
        }
        left += obj.offsetLeft;
        top  += obj.offsetTop;
        return {x:left, y:top};
    }

    function $DOM(A){
        var aL = A.length, node, child, ref={}, bRef=false;
        if(aL>=1){
            node = cE(A[0]);
            if(aL>=2){
                for(var arg in A[1]){
                    if(arg.indexOf('on')==0){
                        node[arg] = A[1][arg];
                    }else if(arg=='ref'){
                        ref[A[1][arg]] = node;
                        ref['DOM']     = node;
                        bRef           = true;
                    }else{
                        if(arg=='style'){
                            node.style.cssText   = A[1][arg];
                        } else if(arg.toLowerCase()=='classname'){
                            node.style.className = A[1][arg];
                        } else {
                            node.setAttribute(arg, A[1][arg]);
                        }
                    }
                }
            }
            for(var i=2; i<aL; i++){
                if(typeof(A[i])=='string'){
                    node.appendChild(document.createTextNode(A[i]));
                } else {
                    child = $DOM(A[i]);
                    if(child.DOM){
                        bRef = true;
                        for(n in child){
                            if(n=='DOM'){
                                node.appendChild(child[n]);
                            }else{
                                ref[n] = child[n];
                            }
                        }
                        ref['DOM'] = node;
                    }else{
                        node.appendChild(child);
                    }
                }
            }
            return bRef?ref:node;
        }
        return null;
    }

    function cE(){
        var A = arguments;

        if(!cE.cache[A[0]]) cE.cache[A[0]]=document.createElement(A[0]);
        return cE.cache[A[0]].cloneNode(false);
    }
    cE.cache    = {};

    function createColorPicker(){
        if(cp) return;
        cp = $DOM(
            ['DIV', {style:'position:absolute;font-family:tahoma,verdana,sans-serif;font-size:10px;', ref:'ColorPicker'},
                ['DIV', {style:'background-color:#FFF;line-height:2px;width:423px;width:424px;height:21px;border:1px solid #000;', ref:'hColorPicker'},
                    ['DIV', {style:'width:50px;height:21px;border-right:1px solid #FFF;float:left;', ref:'hColorDiv'}],
                    ['DIV', {style:'float:left;'},
                        ['IMG', {style:'cursor:crosshair;', src:imgBase+'cp_horizontal_picker.png', width:350, height:21, onmousemove:hColorPickerMouseMove, onmousedown:hColorPickerMouseDown, ref:'hColorImg'}],
                        ['IMG', {style:'cursor:pointer;margin-left:1px;', src:imgBase+'cp_mini_icon.png', width:21, height:21, ref:'hColorIcon', onmousedown:showLgColorPicker}]
                    ]
                ],
                ['DIV', {style:'border:1px solid #000;width:397px;height:305px;position:absolute;background-color:#E0DFE3;', ref:'fColorPicker'},
                    ['DIV', {style:'position:absolute;top:3px;left:8px;'}, 'Select Color:'],
                    ['IMG', {src:imgBase+'cp_lg_background.png', width:260, height:260, style:'position:absolute;top:20px;left:8px;', galleryimg:'no'}],
                    ['IMG', {src:imgBase+'cp_lg_overlay.png', width:256, height:256, style:'cursor:crosshair;position:absolute;top:22px;left:10px;', galleryimg:'no', ref:'fColorImg', onmousedown:cpMouseDown, onmouseup:cpMouseUp, onclick:cpMouseClick}],
                    ['IMG', {src:imgBase+'cp_color_slider.png', width:23, height:260, style:'cursor:pointer;position:absolute;top:20px;left:280px;z-index:2;', ref:'colorSlider', onmousedown:cpSliderMouseDown, onmouseup:cpSliderMouseUp, onclick:cpSliderClick}],
                    ['IMG', {src:imgBase+'cp_arrows.gif', width:41, height:9, style:'cursor:pointer;position:absolute;top:18px;left:271px;z-index:1;', ref:'Arrows', onmousedown:cpSliderMouseDown, onmouseup:cpSliderMouseUp, onclick:cpSliderClick}],
                    ['IMG', {src:imgBase+'cp_cur_color_background.png', width:62, height:70, style:'position:absolute;top:20px;left:315px;'}],
                    ['IMG', {src:imgBase+'cp_web_safe.gif', width:14, height:28, style:'cursor:pointer;position:absolute;top:62px;left:380px;', alt:'Click to Select Web Safe Color', ref:'websafeImg', onclick:selectWebSafeColor}],
                    ['DIV', {style:'z-index:2;position:absolute;top:22px;left:317px;width:58px;height:33px;', ref:'curColorDiv'}],
                    ['DIV', {style:'z-index:2;cursor:pointer;position:absolute;top:55px;left:317px;width:58px;height:33px;', ref:'OrigColorDiv', onclick:resetColor}],
                    ['DIV', {style:'position:absolute;top:103px;left:315px;'}, 'R:'],
                    ['DIV', {style:'position:absolute;top:128px;left:315px;'}, 'G:'],
                    ['DIV', {style:'position:absolute;top:153px;left:315px;'}, 'B:'],
                    ['INPUT', {style:'position:absolute;top:100px;left:330px;width:47px;', ref:'rInput', onchange:setCPColor}],
                    ['INPUT', {style:'position:absolute;top:125px;left:330px;width:47px;', ref:'gInput', onchange:setCPColor}],
                    ['INPUT', {style:'position:absolute;top:150px;left:330px;width:47px;', ref:'bInput', onchange:setCPColor}],
                    ['BUTTON', {style:'position:absolute;bottom:50px;left:315px;width:77px;', ref:'OK', onclick:hColorPickerMouseDown}, 'OK'],
                    ['BUTTON', {style:'position:absolute;bottom:25px;left:315px;width:77px;', ref:'Cancel', onclick:hideColorPicker}, 'Cancel'],
                    ['INPUT', {type:'checkbox', style:'position:absolute;top:280px;left:4px;', ref:'websafeCheckbox'}],
                    ['DIV', {style:'position:absolute;top:284px;left:25px;'}, 'Only Web Colors'],
                    ['IMG', {src:imgBase+'cp_mini_icon.png', height:21, width:21, style:'cursor:pointer;position:absolute;bottom:0px;right:0px;border:1px solid #000;border-width:1px 0px 0px 1px;', ref:'fColorIcon', onmousedown:showSmColorPicker}]
                ]
            ]);

        document.onmousemove         = cpMouseMove;
        cp.baseColor                 = {r:0, g:0, b:0};

        document.body.appendChild(cp.ColorPicker);
        cp.ColorPicker.style.display = 'none';
    }

    function getHorizColor(i, width, height){
        var sWidth = (width)/7;         // "section" width
        var C=i%width;                  // column
        var R=Math.floor(i/(sWidth*7)); // row
        var c=i%sWidth;                 // column in current group
        var r, g, b, h;

        var l=(255/sWidth)*c;           // color percentage

        if(C>=sWidth*6){
            r=g=b=255-l;
        } else {
            h=255-l;

            r=C<sWidth?255:C<sWidth*2?h:C<sWidth*4?0:C<sWidth*5?l:255;
            g=C<sWidth?l:C<sWidth*3?255:C<sWidth*4?h:0;
            b=C<sWidth*2?0:C<sWidth*3?l:C<sWidth*5?255:h;

            if(R<(height/2)){
                var base = 255-(255*2/height)*R;

                r=base+(r*R*2/height);
                g=base+(g*R*2/height);
                b=base+(b*R*2/height);
            }else if(R>(height/2)){
                var base = (height-R)/(height/2);

                r=r*base;
                g=g*base;
                b=b*base;
            }
        }

        return hex(r)+hex(g)+hex(b);
    }

    function getVertColor(i, sZ){
        var n=sZ/6, j=sZ/n, C=i, c=C%n;

        r=C<n?255:C<n*2?255-c*j:C<n*4?0:C<n*5?c*j:255;
        g=C<n*2?0:C<n*3?c*j:C<n*5?255:255-c*j;
        b=C<n?c*j:C<n*3?255:C<n*4?255-c*j:0;

        return {r:r, g:g, b:b};
    }

    function getGradientColor(x, y, Base){
        x = x<0?0:x>255?255:x;
        y = y<0?0:y>255?255:y;

        var r = Math.round((1-(1-(Base.r/255))*(x/255))*(255-y));
        var g = Math.round((1-(1-(Base.g/255))*(x/255))*(255-y));
        var b = Math.round((1-(1-(Base.b/255))*(x/255))*(255-y));

        return {r:r, g:g, b:b};
    }

    function getWebSafeColor(color){
        var rMod = color.r % 51;
        var gMod = color.g % 51;
        var bMod = color.b % 51;

        if((rMod==0) && (gMod==0) && (bMod==0)) return false;

        var wsColor={};

        wsColor.r=(rMod<=25?Math.floor(color.r/51)*51:Math.ceil(color.r/51)*51);
        wsColor.g=(gMod<=25?Math.floor(color.g/51)*51:Math.ceil(color.g/51)*51);
        wsColor.b=(bMod<=25?Math.floor(color.b/51)*51:Math.ceil(color.b/51)*51);

        return wsColor;
    }

    function hColorPickerMouseMove(ev){
        ev            = ev || window.event;
        var hCPImg    = ev.target || ev.srcElement;

        var mousePos  = mouseCoordinates(ev);
        cp.colorPos   = getPosition(hCPImg);

        var x         = mousePos.x-cp.colorPos.x;
        var y         = mousePos.y-cp.colorPos.y;
        var width     = parseInt(hCPImg.offsetWidth);
        var height    = parseInt(hCPImg.offsetHeight);

        var color     = getHorizColor(y*width+x, width, height);

        cp.hColorDiv.style.backgroundColor = cp.cpColor = '#'+color;
    }

    function hColorPickerMouseDown(){
        if(cp.cpColor.r || (cp.cpColor.r===0)) cp.cpColor = '#'+hex(cp.cpColor.r)+hex(cp.cpColor.g)+hex(cp.cpColor.b);
        cp.cpInput.value = cp.cpColor;
        cp.cpButton.style.backgroundColor = cp.cpColor.toString();
        hideColorPicker();
    }

    function attachColorPicker(input, button, noLg){
        createColorPicker();
        if(noLg) input.setAttribute('noLg', '1');
        cp.cpInput = input;
        cp.cpButton = button;
        button.onblur  = function() { tryHideColorPicker(input) };
        button.onclick = function() { showColorPicker(input) };
    }

    function showSmColorPicker(input){
        cp.clicked = true;
        showColorPicker(input, 's');
    }

    function showLgColorPicker(input){
        cp.clicked = true;
        showColorPicker(input, 'l');
    }

    function showColorPicker(input, size){
        size = size || (cp.fColorPicker.style.display=='inline'?'l':'s');

        cp.ColorPicker.style.display  = 'inline';
        cp.hColorIcon.style.display = cp.cpInput.getAttribute('noLg')=='1'?'none':'inline';

        var inpPos = getPosition(cp.cpInput);

        cp.ColorPicker.style.left = inpPos.x + 'px';
        cp.ColorPicker.style.top  = (inpPos.y+parseInt(cp.cpInput.offsetHeight)) + 'px';

        cp.hColorPicker.style.display = cp.fColorPicker.style.display = 'none';
        (size=='s'?cp.hColorPicker:cp.fColorPicker)['style'].display = 'block';

        if(size!='s'){
            cp.baseColor = parseColor(cp.cpInput.value);
            setCPColor(cp.fColorImg.style.backgroundColor = cp.origColor = cp.OrigColorDiv.style.backgroundColor = '#'+hex(cp.baseColor.r)+hex(cp.baseColor.g)+hex(cp.baseColor.b));

            cp.sliderPos = getPosition(cp.colorSlider);
        }
    }

    function tryHideColorPicker(){
        if(!cp.clicked) hideColorPicker();
    }

    function hideColorPicker(){
        cp.ColorPicker.style.display  = 'none';
    }

    function cpMouseDown(ev){
        cp.cpPos       = getPosition(cp.fColorImg);
        cp.cpMouseDown = true;

        return false;
    }

    function cpMouseUp(ev){
        cp.cpMouseDown = false;
    }

    function cpSliderMouseDown(ev){
        cp.csPos           = getPosition(cp.colorSlider);
        cp.SliderMouseDown = true;

        return false;
    }

    function cpSliderMouseUp(ev){
        cp.SliderMouseDown = false;
    }

    function cpSliderClick(ev){
        ev           = ev || window.event;
        var mousePos = mouseCoordinates(ev);

        var y        = mousePos.y-cp.sliderPos.y-4;

        cpSliderSetColor(y);
    }

    function cpMouseClick(ev){
        ev           = ev || window.event;
        var mousePos = mouseCoordinates(ev);

        var x        = mousePos.x-cp.cpPos.x-1;
        var y        = mousePos.y-cp.cpPos.y-1;

        setCPColor(getGradientColor(x, y, cp.baseColor));
    }

    function cpMouseMove(ev){
         // fired when mouse moves over the color picker
        if(cp.cpMouseDown){
            cpMouseClick(ev);
        }

        // fired when mouse moves over the color slider
        if(cp.SliderMouseDown){
            cpSliderClick(ev);
        }

        return false;
    }

    function cpSliderSetColor(y){
        y = y<0?0:y>255?255:y;

        cp.Arrows.style.top      = (y+18)+'px';
        var color = cp.baseColor = getVertColor(y, 256);

        cp.fColorImg.style.backgroundColor = '#'+hex(color.r)+hex(color.g)+hex(color.b);
    }

    function selectWebSafeColor(){
        setCPColor(getWebSafeColor(cp.cpColor));
    }

    function resetColor(){
        setCPColor(cp.origColor);
    }

    function setCPColor(color){
        if(color.srcElement || color.target) color=null;
        if(color && (!color.r && (color.r!=0))) color = parseColor(color);
        if(!color){
            color = {
                r:parseInt(cp.rInput.value),
                g:parseInt(cp.gInput.value),
                b:parseInt(cp.bInput.value)
            }
        }
        var wsColor = getWebSafeColor(color)

        if(wsColor && !cp.websafeCheckbox.checked){
            cp.websafeImg.style.display         = 'block';
            cp.websafeImg.style.backgroundColor = '#'+hex(wsColor.r)+hex(wsColor.g)+hex(wsColor.b);
        }else{
            if(wsColor && cp.websafeCheckbox.checked) color = wsColor;
            cp.websafeImg.style.display         = 'none';
        }

        cp.rInput.value = color.r;
        cp.gInput.value = color.g;
        cp.bInput.value = color.b;

        cp.cpColor      = color;
        cp.curColorDiv.style.backgroundColor = '#'+hex(color.r)+hex(color.g)+hex(color.b);
    }

    function parseColor(text){
        if(/^\#?[0-9A-F]{6}$/i.test(text)){
            return {
                r: eval('0x'+text.substr(text.length==6?0:1, 2)),
                g: eval('0x'+text.substr(text.length==6?2:3, 2)),
                b: eval('0x'+text.substr(text.length==6?4:5, 2))
            }
        }
        return {r:255, g:0, b:0};
    }

    function documentMouseDown(ev){
        ev         = ev            || window.event;
        var target = ev.srcElement || ev.target;

        while(target){
            if(target==cp.ColorPicker) return;
            target = target.parentNode;
        }
        cp.ColorPicker.style.display = 'none';
    }

    function documentMouseUp(ev){
        cpMouseUp(ev);
        cpSliderMouseUp(ev);
    }

    document.onmousedown     = documentMouseDown;
    document.onmouseup       = documentMouseUp;
    window.attachColorPicker = attachColorPicker;
})();