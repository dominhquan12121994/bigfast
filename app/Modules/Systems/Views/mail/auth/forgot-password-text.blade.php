Hello {{ $mail->receiver }},
This is a demo email for testing purposes! Also, it's the HTML version.

Demo object values:

Demo One: {{ $mail->demo_one }}
Demo Two: {{ $mail->demo_two }}

Values passed by With method:

testVarOne: {{ $testVarOne }}
testVarOne: {{ $testVarOne }}

Thank You,
{{ $mail->sender }}
