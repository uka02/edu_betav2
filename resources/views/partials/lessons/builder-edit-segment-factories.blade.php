        function addContentSegment() {
            contentSegmentIndex++;
            const newSegmentId = segmentCounter++;
            segments.push({ id: newSegmentId, label: `${i18n.contentSegment} ${contentSegmentIndex}`, type: 'content', customName: '' });
            const segmentHtml = renderContentSegment(newSegmentId, { custom_name: '', blocks: [] });
            document.getElementById('segmentsContainer').insertAdjacentHTML('beforeend', segmentHtml);
            initializeSegments();
            switchSegment(newSegmentId);
        }

        function addExamSegment() {
            const newSegmentId = segmentCounter++;
            segments.push({ id: newSegmentId, label: `${i18n.examIndexLabel}`, type: 'exam', customName: '' });
            const segmentHtml = renderExamSegment(newSegmentId, { custom_name: '', exam_settings: { time_limit: 0, passing_score: 60 }, questions: [] });
            document.getElementById('segmentsContainer').insertAdjacentHTML('beforeend', segmentHtml);
            initializeSegments();
            switchSegment(newSegmentId);
        }

        function removeSegment(segmentId) {
            if (segmentId === 0) { alert(i18n.cannotRemoveBasic); return; }
            if (!confirm(i18n.confirmRemoveSegment)) return;
            segments = segments.filter(s => s.id !== segmentId);
            const segment = document.querySelector(`.segment-container[data-segment="${segmentId}"]`);
            if (segment) segment.remove();
            initializeSegments();
            switchSegment(0);
        }
