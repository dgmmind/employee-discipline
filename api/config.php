<?php
// Rellena con tus credenciales. IMPORTANTE: usa la service_role SOLO en el servidor (PHP), nunca en el frontend.
// Puedes tomar los valores de variables de entorno si las defines en tu sistema.

const SUPABASE_URL = 'https://nlxwpskjjujzmumzdsip.supabase.co';
const SUPABASE_SERVICE_ROLE = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5seHdwc2tqanVqem11bXpkc2lwIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc1MTM0ODYyNiwiZXhwIjoyMDY2OTI0NjI2fQ.RRxXSmSz-7AliAbL8pkt9k_15MNZoQZ47NC7wsgCC48';

// Endpoint base del REST de Supabase
const SUPABASE_REST_URL = SUPABASE_URL . '/rest/v1';

// Estados de evaluación
define('EVALUATION_STATUS_PERFECTO', 'Perfecto');
