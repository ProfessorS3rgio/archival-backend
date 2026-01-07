


# Archival System API Documentation

## Overview
The Archival System provides a REST API for accessing archived documents and their associated PDF files. This API allows external systems to query, preview, and download documents from the archive. All document files are PDFs that can be previewed inline or downloaded.

**Available Document Types:**
- Resolutions
- Ordinances
- Municipal Communications
- Draft Resolutions
- Draft Ordinances
- Accomplishments
- Projects
- Memorandums of Agreement
- Committee Reports
- National Office Communications
- Provincial Office Communications

## Base URL
```
http://127.0.0.1:8000/api/v1/
```

For production:
```
https://your-domain.com/api/v1/
```

## Response Format

All API responses follow a consistent JSON structure:

### Paginated List Response
```json
{
  "success": true,
  "data": {
    "items": [...],
    "meta": {
      "current_page": 1,
      "per_page": 15,
      "total": 100,
      "last_page": 7,
      "from": 1,
      "to": 15
    },
    "links": {
      "first": "http://127.0.0.1:8000/api/v1/Documents/Resolution?page=1",
      "last": "http://127.0.0.1:8000/api/v1/Documents/Resolution?page=7",
      "prev": null,
      "next": "http://127.0.0.1:8000/api/v1/Documents/Resolution?page=2"
    }
  },
  "message": "Success message"
}
```

### Single Item Response
```json
{
  "success": true,
  "data": {
    "id": 1,
    ...
  },
  "message": "Success message"
}
```

### Error Response
```json
{
  "success": false,
  "data": null,
  "message": "Error message"
}
```

## Common Endpoints

### Get Resolutions
Returns all resolutions with their PDF file preview and download URLs.

**GET** `/api/v1/Documents/Resolution`

**Query Parameters:**
- `search` (optional): Search term
- `author` (optional): Filter by author
- `year` (optional): Filter by year (e.g., 2024)
- `status` (optional): Filter by status ("approved" or "pending")
- `type` (optional): Filter by type ("municipal" or "provincial")
- `per_page` (optional): Items per page (default: 15)

**Example Request:**
```
GET /api/v1/Documents/Resolution?search=budget&year=2024&per_page=10
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 1,
        "resolution_no": "2024-001",
        "title": "Budget Resolution 2024",
        "author": "Hon. John Doe",
        "subject": "Annual Budget",
        "date_approved": "2024-01-15T00:00:00.000000Z",
        "created_at": "2024-01-10T10:00:00.000000Z",
        "updated_at": "2024-01-15T14:30:00.000000Z",
        "files": [
          {
            "id": 1,
            "filename": "budget-resolution-2024.pdf",
            "original_filename": "budget-resolution-2024.pdf",
            "file_size": 245760,
            "mime_type": "application/pdf",
            "download_url": "http://127.0.0.1:8000/api/v1/Documents/Resolution/1/files/1/download",
            "preview_url": "http://127.0.0.1:8000/api/v1/Documents/Resolution/1/files/1/download?preview=1"
          }
        ]
      }
    ],
    "meta": {
      "current_page": 1,
      "per_page": 15,
      "total": 67,
      "last_page": 5,
      "from": 1,
      "to": 15
    },
    "links": {
      "first": "http://127.0.0.1:8000/api/v1/Documents/Resolution?page=1",
      "last": "http://127.0.0.1:8000/api/v1/Documents/Resolution?page=5",
      "prev": null,
      "next": "http://127.0.0.1:8000/api/v1/Documents/Resolution?page=2"
    }
  },
  "message": "Resolutions retrieved successfully"
}
```

### Get Single Resolution
Returns a specific resolution by ID.

**GET** `/api/v1/Documents/Resolution/{id}`

**Example Request:**
```
GET /api/v1/Documents/Resolution/1
```

### Get Ordinances
Returns all ordinances with their PDF file preview and download URLs.

**GET** `/api/v1/Documents/Ordinance`

**Query Parameters:**
- `search` (optional): Search term
- `author` (optional): Filter by author
- `year` (optional): Filter by year
- `status` (optional): Filter by status ("approved" or "pending")
- `per_page` (optional): Items per page (default: 15)

**Example Request:**
```
GET /api/v1/Documents/Ordinance?year=2024
```

### Get Single Ordinance
Returns a specific ordinance by ID.

**GET** `/api/v1/Documents/Ordinance/{id}`

### Get Municipal Communications
Returns all municipal communications with their PDF file preview and download URLs.

**GET** `/api/v1/Documents/MunicipalCommunication`

**Query Parameters:**
- `search` (optional): Search term
- `year` (optional): Filter by year
- `municipality` (optional): Filter by municipality
- `per_page` (optional): Items per page (default: 15)

### Get Single Municipal Communication
Returns a specific municipal communication by ID.

**GET** `/api/v1/Documents/MunicipalCommunication/{id}`

### Get Draft Resolutions
Returns all draft resolutions with their PDF file preview and download URLs.

**GET** `/api/v1/Documents/DraftResolution`

**Query Parameters:**
- `search` (optional): Search term
- `author` (optional): Filter by author
- `year` (optional): Filter by year
- `status` (optional): Filter by status
- `per_page` (optional): Items per page (default: 15)

### Get Single Draft Resolution
Returns a specific draft resolution by ID.

**GET** `/api/v1/Documents/DraftResolution/{id}`

### Get Draft Ordinances
Returns all draft ordinances with their PDF file preview and download URLs.

**GET** `/api/v1/Documents/DraftOrdinance`

**Query Parameters:**
- `search` (optional): Search term
- `author` (optional): Filter by author
- `year` (optional): Filter by year
- `status` (optional): Filter by status
- `per_page` (optional): Items per page (default: 15)

### Get Single Draft Ordinance
Returns a specific draft ordinance by ID.

**GET** `/api/v1/Documents/DraftOrdinance/{id}`

### Get Accomplishments
Returns all accomplishments with their PDF file preview and download URLs.

**GET** `/api/v1/Documents/Accomplishment`

**Query Parameters:**
- `search` (optional): Search term
- `year` (optional): Filter by year
- `per_page` (optional): Items per page (default: 15)

### Get Single Accomplishment
Returns a specific accomplishment by ID.

**GET** `/api/v1/Documents/Accomplishment/{id}`

### Get Projects
Returns all projects with their PDF file preview and download URLs.

**GET** `/api/v1/Documents/Project`

**Query Parameters:**
- `search` (optional): Search term
- `year` (optional): Filter by year
- `district` (optional): Filter by district
- `source_fund` (optional): Filter by source fund
- `per_page` (optional): Items per page (default: 15)

### Get Single Project
Returns a specific project by ID.

**GET** `/api/v1/Documents/Project/{id}`

### Get Memorandums of Agreement
Returns all memorandums of agreement with their PDF file preview and download URLs.

**GET** `/api/v1/Documents/MemorandumOfAgreement`

**Query Parameters:**
- `search` (optional): Search term
- `year` (optional): Filter by year
- `resolution_no` (optional): Filter by resolution number
- `per_page` (optional): Items per page (default: 15)

### Get Single Memorandum of Agreement
Returns a specific memorandum of agreement by ID.

**GET** `/api/v1/Documents/MemorandumOfAgreement/{id}`

### Get Committee Reports
Returns all committee reports with their PDF file preview and download URLs.

**GET** `/api/v1/Documents/CommitteeReport`

**Query Parameters:**
- `search` (optional): Search term
- `year` (optional): Filter by year
- `per_page` (optional): Items per page (default: 15)

### Get Single Committee Report
Returns a specific committee report by ID.

**GET** `/api/v1/Documents/CommitteeReport/{id}`

### Get National Office Communications
Returns all national office communications with their PDF file preview and download URLs.

**GET** `/api/v1/Documents/NationalOfficeCommunication`

**Query Parameters:**
- `search` (optional): Search term
- `year` (optional): Filter by year
- `per_page` (optional): Items per page (default: 15)

### Get Single National Office Communication
Returns a specific national office communication by ID.

**GET** `/api/v1/Documents/NationalOfficeCommunication/{id}`

### Get Provincial Office Communications
Returns all provincial office communications with their PDF file preview and download URLs.

**GET** `/api/v1/Documents/ProvincialOffice`

**Query Parameters:**
- `search` (optional): Search term
- `year` (optional): Filter by year
- `per_page` (optional): Items per page (default: 15)

## Complete API Endpoint List

### Clean RESTful Endpoints

#### Resolutions
- `GET /api/v1/Documents/Resolution` - List all resolutions
- `GET /api/v1/Documents/Resolution/{id}` - Get single resolution
- `GET /api/v1/Documents/Resolution/{id}/files/{file_id}/download` - Download resolution file
- `GET /api/v1/Documents/Resolution/{id}/files/{file_id}/download?preview=1` - Preview resolution file

#### Ordinances
- `GET /api/v1/Documents/Ordinance` - List all ordinances
- `GET /api/v1/Documents/Ordinance/{id}` - Get single ordinance
- `GET /api/v1/Documents/Ordinance/{id}/files/{file_id}/download` - Download ordinance file
- `GET /api/v1/Documents/Ordinance/{id}/files/{file_id}/download?preview=1` - Preview ordinance file

#### Municipal Communications
- `GET /api/v1/Documents/MunicipalCommunication` - List all municipal communications
- `GET /api/v1/Documents/MunicipalCommunication/{id}` - Get single municipal communication
- `GET /api/v1/Documents/MunicipalCommunication/{id}/download` - Download municipal communication file
- `GET /api/v1/Documents/MunicipalCommunication/{id}/download?preview=1` - Preview municipal communication file

#### Draft Resolutions
- `GET /api/v1/Documents/DraftResolution` - List all draft resolutions
- `GET /api/v1/Documents/DraftResolution/{id}` - Get single draft resolution
- `GET /api/v1/Documents/DraftResolution/{id}/files/{file_id}/download` - Download draft resolution file
- `GET /api/v1/Documents/DraftResolution/{id}/files/{file_id}/download?preview=1` - Preview draft resolution file

#### Draft Ordinances
- `GET /api/v1/Documents/DraftOrdinance` - List all draft ordinances
- `GET /api/v1/Documents/DraftOrdinance/{id}` - Get single draft ordinance
- `GET /api/v1/Documents/DraftOrdinance/{id}/files/{file_id}/download` - Download draft ordinance file
- `GET /api/v1/Documents/DraftOrdinance/{id}/files/{file_id}/download?preview=1` - Preview draft ordinance file

#### Accomplishments
- `GET /api/v1/Documents/Accomplishment` - List all accomplishments
- `GET /api/v1/Documents/Accomplishment/{id}` - Get single accomplishment
- `GET /api/v1/Documents/Accomplishment/{id}/download` - Download accomplishment file
- `GET /api/v1/Documents/Accomplishment/{id}/download?preview=1` - Preview accomplishment file

#### Projects
- `GET /api/v1/Documents/Project` - List all projects
- `GET /api/v1/Documents/Project/{id}` - Get single project
- `GET /api/v1/Documents/Project/{id}/download` - Download project file
- `GET /api/v1/Documents/Project/{id}/download?preview=1` - Preview project file

#### Memorandums of Agreement
- `GET /api/v1/Documents/MemorandumOfAgreement` - List all memorandums of agreement
- `GET /api/v1/Documents/MemorandumOfAgreement/{id}` - Get single memorandum of agreement
- `GET /api/v1/Documents/MemorandumOfAgreement/{id}/download` - Download memorandum of agreement file
- `GET /api/v1/Documents/MemorandumOfAgreement/{id}/download?preview=1` - Preview memorandum of agreement file

#### Committee Reports
- `GET /api/v1/Documents/CommitteeReport` - List all committee reports
- `GET /api/v1/Documents/CommitteeReport/{id}` - Get single committee report
- `GET /api/v1/Documents/CommitteeReport/{id}/download` - Download committee report file
- `GET /api/v1/Documents/CommitteeReport/{id}/download?preview=1` - Preview committee report file

#### National Office Communications
- `GET /api/v1/Documents/NationalOfficeCommunication` - List all national office communications
- `GET /api/v1/Documents/NationalOfficeCommunication/{id}` - Get single national office communication
- `GET /api/v1/Documents/NationalOfficeCommunication/{id}/download` - Download national office communication file
- `GET /api/v1/Documents/NationalOfficeCommunication/{id}/download?preview=1` - Preview national office communication file

#### Provincial Office Communications
- `GET /api/v1/Documents/ProvincialOffice` - List all provincial office communications
- `GET /api/v1/Documents/ProvincialOffice/{id}` - Get single provincial office communication
- `GET /api/v1/Documents/ProvincialOffice/{id}/download` - Download provincial office communication file
- `GET /api/v1/Documents/ProvincialOffice/{id}/download?preview=1` - Preview provincial office communication file

### Generic Endpoints
- `GET /api/v1/Documents/{type}` - List documents by type (for additional document types)
- `GET /api/v1/Documents/{type}/{document}/files/{file}/download` - Download file from generic document type
- `GET /api/v1/Documents/{type}/{document}/files/{file}/download?preview=1` - Preview file from generic document type

### Legacy Endpoints (Backward Compatibility)
- `GET /api/v1/documents/types` - Get available document types
- `GET /api/v1/documents/{type}` - List documents by type (legacy)
- `GET /api/v1/resolutions` - List resolutions (legacy)
- `GET /api/v1/resolutions/{id}` - Get single resolution (legacy)
- `GET /api/v1/ordinances` - List ordinances (legacy)
- `GET /api/v1/ordinances/{id}` - Get single ordinance (legacy)

## File Download and Preview

### Download Document File
Downloads a PDF file associated with a document.

**Resolutions & Ordinances (with multiple files):**
```
GET /api/v1/Documents/Resolution/{id}/files/{file_id}/download
GET /api/v1/Documents/Ordinance/{id}/files/{file_id}/download
GET /api/v1/Documents/DraftResolution/{id}/files/{file_id}/download
GET /api/v1/Documents/DraftOrdinance/{id}/files/{file_id}/download
```

**Single-file documents:**
```
GET /api/v1/Documents/MunicipalCommunication/{id}/download
GET /api/v1/Documents/Accomplishment/{id}/download
GET /api/v1/Documents/Project/{id}/download
GET /api/v1/Documents/MemorandumOfAgreement/{id}/download
GET /api/v1/Documents/CommitteeReport/{id}/download
GET /api/v1/Documents/NationalOfficeCommunication/{id}/download
GET /api/v1/Documents/ProvincialOffice/{id}/download
```

**Example:**
```
GET /api/v1/Documents/Ordinance/1/files/1/download
```

Returns the file as a downloadable attachment with proper headers.

### Preview Document File (Inline)
Returns the PDF file for inline preview in the browser instead of downloading.

Add `?preview=1` query parameter to any download URL:

**Examples:**
```
GET /api/v1/Documents/Ordinance/1/files/1/download?preview=1
GET /api/v1/Documents/Project/5/download?preview=1
GET /api/v1/Documents/CommitteeReport/3/download?preview=1
```

Returns the PDF with `Content-Disposition: inline` header for browser preview.

## Additional Endpoints

### Get Document Types
Returns all available document types.

**GET** `/api/v1/documents/types`

**Example Response:**
```json
{
  "success": true,
  "data": {
    "resolutions": "Resolutions",
    "ordinances": "Ordinances",
    "draft-resolutions": "Draft Resolutions",
    "draft-ordinances": "Draft Ordinances",
    "accomplishments": "Accomplishments",
    "projects": "Projects",
    "moas": "Memorandum of Agreements",
    "municipal-communications": "Municipal Communications",
    "national-offices": "National Office Communications",
    "provincial-offices": "Provincial Office Communications",
    "committee-reports": "Committee Reports"
  },
  "message": "Document types retrieved successfully"
}
```

## Error Responses

### Not Found (404)
```json
{
  "success": false,
  "data": null,
  "message": "Document not found"
}
```

### Bad Request (400)
```json
{
  "success": false,
  "data": null,
  "message": "Invalid document type"
}
```
## Examples

### JavaScript (Fetch API)
```javascript
const BASE_URL = 'http://127.0.0.1:8000/api/v1';

// Get document types
fetch(`${BASE_URL}/documents/types`)
  .then(response => response.json())
  .then(data => console.log(data));

// Get ordinances
fetch(`${BASE_URL}/Documents/Ordinance?year=2024`)
  .then(response => response.json())
  .then(data => console.log(data));

// Download a file
fetch(`${BASE_URL}/Documents/Ordinance/1/files/1/download`)
  .then(response => response.blob())
  .then(blob => {
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'ordinance.pdf';
    a.click();
  });

// Preview a PDF file inline
fetch(`${BASE_URL}/Documents/Ordinance/1/files/1/download?preview=1`)
  .then(response => response.blob())
  .then(blob => {
    const url = window.URL.createObjectURL(blob);
    const iframe = document.createElement('iframe');
    iframe.src = url;
    iframe.width = '100%';
    iframe.height = '600px';
    document.body.appendChild(iframe);
  });
```

### PHP (cURL)
```php
$baseUrl = 'http://127.0.0.1:8000/api/v1';

// Get documents
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/Documents/Resolution?search=budget');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

// Download file
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/Documents/Resolution/1/files/1/download');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$fileContent = curl_exec($ch);
curl_close($ch);

file_put_contents('resolution.pdf', $fileContent);
```

### Python (requests)
```python
import requests

base_url = 'http://127.0.0.1:8000/api/v1'

# Get documents
response = requests.get(f'{base_url}/Documents/Resolution', params={
    'search': 'budget',
    'year': 2024
})
data = response.json()
print(data)

# Download file
response = requests.get(f'{base_url}/Documents/Resolution/1/files/1/download')
with open('resolution.pdf', 'wb') as f:
    f.write(response.content)

# Preview URL (for embedding in iframe)
preview_url = f'{base_url}/Documents/Resolution/1/files/1/download?preview=1'
```

## Notes

- All endpoints return JSON responses with the format: `{ success, data, message }`
- Pagination uses `{ items, meta, links }` structure in the `data` object
- PDF files are served from private storage and accessed via the API
- Use `?preview=1` parameter on any download URL to preview inline instead of downloading
- All timestamps are in ISO 8601 format with timezone
- File sizes are in bytes