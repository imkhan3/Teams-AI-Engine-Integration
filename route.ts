import { NextResponse } from "next/server";
import crypto from 'crypto';

const API_KEY: string = process.env.DATA_API_KEY as string;
const bufSecret = new Buffer(process.env.TEAMS_API_KEY as string, 'base64');

export async function POST(request: Request) {
    const payload  =  await request.text();
    const text = JSON.parse(payload).text;
    const auth = request.headers.get('Authorization');
    const msgBuf = Buffer.from(payload, 'utf8');
    console.log(msgBuf);
    const msgHash = 'HMAC ' + crypto.createHmac('sha256', bufSecret).update(msgBuf).digest('base64');
    if (msgHash != auth) return NextResponse.json( msgBuf)
        
    else{
        return NextResponse.json({ "type": "message", "text": `Success: ${text}` })
    }
}