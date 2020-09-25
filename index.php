<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCP TEST</title>
</head>
<style>
    body{
        font-size:19px;
    }
    label{
        color:red;
    }
    .out{
        color:red;
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded",()=>{
        var btnCal = document.getElementById("btn_cal")
        var freq = document.getElementById("freq")
        var duty = document.getElementById("duty")
        var cyt = document.getElementById("cyt")

        var lfreq = document.getElementById("label_freq")
        var lduty = document.getElementById("label_Duty")
        var lcyt = document.getElementById("label_Cytal")
        


        const checkValue = (val) => {
            if(val == null || val == "" || !val){
                return false
            }
            return true
        }

        const findT = (fq) => {
            let T = 1/fq
            return T.toExponential(3);
        }

        const findTon = (duty,t) => {
            let Ton = (duty * t)/100
            return Ton.toExponential(3);
        }

        const TimePrescaler = (f) => {
            f = f / 1000

            if(f>=1.22 && f<4.88){
                return 16
            }else if(f >= 4.88 && f < 19.53 ){
                return 4
            }else if(f >= 19.53 && f < 78.12){
                return 1
            }else if(f >= 78.12 && f < 156.3){
                return 1
            }else {
                return 1
            }
            

        }
        const Tosc = (ct) => {
            let tosc = 1/ct
            return tosc.toExponential(3)
        }

        const PR2 = (t,tosc,timescaler) => {
            let res = t/(4 * tosc *timescaler)
            return Math.floor(res)
        }

        const CCP1CON54 = (ton, tosc, timescaler) => {
            let res = ton / (tosc * timescaler)
            res = Math.floor(res)
            console.log(res)
            let binRes = res.toString(2).split("")
            let len = binRes.length

            for(let i=len;i<10;i++){
                binRes.unshift('0')
                
            }
            let fiveFour = `${binRes[8]}${binRes[9]}`
            let CCP1CON = binRes.slice(0, 8)
            CCP1CON = parseInt(CCP1CON.join(""),2).toString(16)

            if(CCP1CON.length < 2){
                CCP1CON =  `0${CCP1CON}`
            }

            return {
                ccpr1l: `0x${CCP1CON}`,
                ValueSplit: fiveFour,
            }
        }

        const CCP1CON = (ff) => {
            let buffer = ['0','0','1','1','0','0']
            ff = ff.split("")
            buffer.splice(2,0,ff[0],ff[1])
            console.log(buffer)
            buffer = parseInt(buffer.join(""),2).toString(16)
            if(buffer.length < 2){
                buffer =  `0${buffer}`
            }
            return `0x${buffer}`

        }

        const T2CON = (timeScaler) => {
            let buffer = ['0','0','0','0','0','1']
            timeScaler = parseInt(timeScaler)
            if(timeScaler == 1){
                buffer.splice(buffer.length, 0, '0','0')
            }else if(timeScaler == 4){
                buffer.splice(buffer.length, 0, '0','1')
            }else if(timeScaler == 16){
                buffer.splice(buffer.length, 0, '1','0')
            }
            buffer = parseInt(buffer.join(""),2).toString(16)
            if(buffer.length < 2){
                buffer =  `0${buffer}`
            }
            return `0x${buffer}`
        }

        const cale = () => {
            var _freq = parseFloat(freq.value) * 1000
            var _duty = parseFloat(duty.value)
            var _cyt = parseFloat(cyt.value) * 1000000
            let T = findT(_freq)
            let Ton = findTon(_duty, T)
            let TimePres = TimePrescaler(_freq)
            let _Tosc = Tosc(_cyt)
            let _PR2 = PR2(T, _Tosc, TimePres)
            let _CCP1CON54 = CCP1CON54(Ton, _Tosc, TimePres)
            let _CCP1CON = CCP1CON(_CCP1CON54.ValueSplit)
            let _T2CON = T2CON(TimePres)
            
            lfreq.innerHTML = freq.value
            lduty.innerHTML = duty.value
            lcyt.innerHTML = cyt.value

            document.getElementById("pr2").innerHTML = _PR2;
            document.getElementById("ccpr1l").innerHTML = _CCP1CON54.ccpr1l;
            document.getElementById("ccp1con").innerHTML = _CCP1CON;
            document.getElementById("t2con").innerHTML = _T2CON;

            document.getElementById("T").innerHTML = T;
            document.getElementById("Ton").innerHTML = Ton;
            document.getElementById("Tosc").innerHTML = _Tosc;
            document.getElementById("Tmp").innerHTML = TimePres;
            
        }

        btnCal.addEventListener("click", ()=>{
            if(checkValue(freq.value) && checkValue(duty.value) && checkValue(cyt.value)){
                cale()
                return 1
            }
            alert('Please input data.')
        })
    })
</script>
<body>
    <p>No Stylesheet developed by <a target="_blank" href="https://www.github.com/suthiphong">@JingJo.MUT</a></p>
    <p>Example : frequency : 70kHz, Duty : 10%, Cytal: 10MHz</p>
    <table>
        <tr>
            <td>frequency(kHz): </td>
            <td>Duty(%) : </td>
            <td>Cytal (Mhz) : </td>
            <td></td>
        </tr>
        <tr>
            <td><input type="text" placeholder="70" id="freq" value="70"></td>
            <td><input type="text" placeholder="10" id="duty" value="10"></td>
            <td><input type="text" placeholder="10" id="cyt" value="10"></td>
            <td><button id="btn_cal">Cal</button></td>
        </tr>
    </table>

    <div>
        Input : frequency : <label id="label_freq"></label> kHz
        Duty(%) : <label id="label_Duty"></label>
        Cytal(Mhz) : <label id="label_Cytal"></label>
        <hr>
        <table>
            <tr>
                <td>PR2 : </td>
                <td id="pr2" class="out"></td>
            </tr>
            <tr>
                <td>CCPR1L : </td>
                <td id="ccpr1l" class="out"></td>
            </tr>
            <tr>
                <td>CCP1CON : </td>
                <td id="ccp1con" class="out"></td>
            </tr>
            <tr>
                <td>T2CON : </td>
                <td id="t2con" class="out"></td>
            </tr>

            <tr>
                <td>Time Prescaler</td> 
                <td id="Tmp"></td>
            </tr>
            <tr> 
                <td>T : </td>
                <td id="T"></td>
            </tr>
            <tr>
                <td>Ton : </td>
                <td id="Ton"></td>
            </tr>
            <tr>
                <td>Tosc : </td>
                <td id="Tosc"></td>
            </tr>
        </table>
    </div>

</body>
</html>