<!-- Modal (Transfer to Agent) -->
<div class="modal fade" id="transferAgentModal" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel">Transfer Property to Agent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="transferAgentForm" onsubmit="handleTransferAgentSubmit(event)">
                    <input type="hidden" id="transferAgentPropertyIds" name="transferAgentPropertyIds">

                    <div class="form-group">
                        <label for="listing_agent" class="block text-sm font-medium mb-2">Listing Agent <span class="text-danger">*</span></label>
                        <select id="listing_agent" name="listing_agent" class="py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none" required>
                            <option value="">Please select</option>
                            <?php
                            $agents_result = CRest::call('crm.item.list', [
                                'entityTypeId' => AGENTS_ENTITY_TYPE_ID,
                                'select' => ['ufCrm7AgentId', 'ufCrm7AgentName']
                            ]);
                            $listing_agents = $agents_result['result']['items'] ?? [];

                            if (empty($listing_agents)) {
                                echo '<option disabled>No agents found</option>';
                            } else {
                                foreach ($listing_agents as $agent) {
                                    echo '<option value="' . htmlspecialchars($agent['ufCrm7AgentId']) . '">' . htmlspecialchars($agent['ufCrm7AgentName']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary" id="transferAgentBtn">
                            Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    async function updateItem(entityTypeId, fields, id) {
        try {
            const response = await fetch(`https://b24-oy9apg.bitrix24.com/rest/9/e3hbkx5cs7wy7r7r/crm.item.update?entityTypeId=${entityTypeId}&id=${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    fields
                })
            });

            if (!response.ok) throw new Error('Failed to update item');
            console.log('Item updated successfully');
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function getAgent(agentId) {
        const response = await fetch(`https://b24-oy9apg.bitrix24.com/rest/9/e3hbkx5cs7wy7r7r/crm.item.list?entityTypeId=1040&filter[ufCrm7AgentId]=${agentId}`);
        return (await response.json()).result.items[0] || null;
    }

    async function handleTransferAgentSubmit(e) {
        document.getElementById("transferAgentBtn").disabled = true;
        document.getElementById("transferAgentBtn").innerHTML = 'Transferring...';

        e.preventDefault();

        const formData = new FormData(e.target);
        const agent = await getAgent(formData.get('listing_agent'));
        if (!agent) return console.error('Agent not found');

        const fields = {
            "ufCrm5AgentId": agent.ufCrm7AgentId,
            "ufCrm5AgentName": agent.ufCrm7AgentName,
            "ufCrm5AgentEmail": agent.ufCrm7AgentName,
            "ufCrm5AgentPhone": agent.ufCrm7AgentMobile,
            "ufCrm5AgentPhoto": agent.ufCrm7AgentPhoto,
            "ufCrm5AgentLicense": agent.ufCrm7AgentLicense
        };

        const propertyIds = formData.get('transferAgentPropertyIds').split(',');

        for (const id of propertyIds) {
            await updateItem(1036, fields, Number(id));
        }

        window.location.href = '?page=properties';
    }
</script>