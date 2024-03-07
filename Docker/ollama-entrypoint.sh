#!/bin/bash

echo "Container started..."
ollama serve
ollama run  mistral:latest
echo "Mistral downloaded"

# Keep the container running after the command has finished.
exec "$@"