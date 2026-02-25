<h2>Nuevo mensaje desde la web</h2>

<p><strong>Nombre:</strong> {{ $msg->nombre }}</p>
<p><strong>Email:</strong> {{ $msg->email }}</p>
@if($msg->telefono)
  <p><strong>Teléfono:</strong> {{ $msg->telefono }}</p>
@endif
<p><strong>Asunto:</strong> {{ $msg->asunto }}</p>

<hr>

<p style="white-space: pre-line;">{{ $msg->mensaje }}</p>